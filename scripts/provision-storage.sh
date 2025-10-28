#!/usr/bin/env bash
# Provision a new storage device for the Carpentry app and bind-mount it to Laravel storage
# Usage:
#   sudo ./provision-storage.sh /dev/sdX [--fs xfs|ext4] [--mount /mnt/carpentry-data] [--app /var/www/carpentry-app] [--owner www-data:www-data] [--skip-artisan]
#
# Example:
#   sudo ./provision-storage.sh /dev/sdb --fs xfs --mount /mnt/carpentry-data --app /var/www/carpentry-app
#
# This script will:
#   - Create filesystem on the device if missing (XFS default)
#   - Create a mountpoint and add an /etc/fstab entry using UUID
#   - Mount the device
#   - Bind-mount the data mountpoint to APP_PATH/storage/app/public
#   - Ensure Laravel storage:link exists
#   - Set permissions to the provided owner (default www-data)
set -euo pipefail

DEVICE="${1:-}"
shift || true

FS_TYPE="xfs"
DATA_MOUNT="/mnt/carpentry-data"
APP_PATH="/var/www/carpentry-app"
OWNER="www-data:www-data"
RUN_ARTISAN=1

while [[ $# -gt 0 ]]; do
  case "$1" in
    --fs)
      FS_TYPE="${2:-xfs}"; shift 2 ;;
    --mount)
      DATA_MOUNT="${2:-/mnt/carpentry-data}"; shift 2 ;;
    --app)
      APP_PATH="${2:-/var/www/carpentry-app}"; shift 2 ;;
    --owner)
      OWNER="${2:-www-data:www-data}"; shift 2 ;;
    --skip-artisan)
      RUN_ARTISAN=0; shift ;;
    -h|--help)
      echo "Usage: sudo $0 /dev/sdX [--fs xfs|ext4] [--mount /mnt/carpentry-data] [--app /var/www/carpentry-app] [--owner user:group] [--skip-artisan]"; exit 0 ;;
    *) echo "Unknown argument: $1"; exit 1 ;;
  esac
done

if [[ "$EUID" -ne 0 ]]; then
  echo "[ERROR] Please run as root (use sudo)." >&2
  exit 1
fi

if [[ -z "$DEVICE" ]]; then
  echo "[ERROR] Device path is required (e.g., /dev/sdb)." >&2
  exit 1
fi

if [[ ! -b "$DEVICE" ]]; then
  echo "[ERROR] $DEVICE is not a block device." >&2
  lsblk
  exit 1
fi

if ! command -v blkid >/dev/null 2>&1; then
  echo "[ERROR] blkid not found. Install util-linux." >&2
  exit 1
fi

if [[ "$FS_TYPE" != "xfs" && "$FS_TYPE" != "ext4" ]]; then
  echo "[ERROR] --fs must be xfs or ext4." >&2
  exit 1
fi

# Ensure mountpoints exist
mkdir -p "$DATA_MOUNT"
mkdir -p "$APP_PATH/storage/app/public"

# Detect existing filesystem
EXISTING_FS="$(blkid -o value -s TYPE "$DEVICE" || true)"
if [[ -z "$EXISTING_FS" ]]; then
  echo "[INFO] No filesystem detected on $DEVICE. Creating $FS_TYPE..."
  if [[ "$FS_TYPE" == "xfs" ]]; then
    mkfs.xfs -f "$DEVICE"
  else
    mkfs.ext4 -F "$DEVICE"
  fi
else
  echo "[INFO] Existing filesystem detected on $DEVICE: $EXISTING_FS (will reuse)"
fi

# Get UUID
UUID="$(blkid -o value -s UUID "$DEVICE")"
if [[ -z "$UUID" ]]; then
  echo "[ERROR] Could not determine UUID for $DEVICE" >&2
  exit 1
fi

# Add /etc/fstab entry for data mount if missing
FSTAB_LINE_DATA="UUID=$UUID $DATA_MOUNT $FS_TYPE defaults 0 2"
if ! grep -qE "^UUID=$UUID\s+$DATA_MOUNT\s+" /etc/fstab; then
  echo "$FSTAB_LINE_DATA" >> /etc/fstab
  echo "[INFO] Added to /etc/fstab: $FSTAB_LINE_DATA"
else
  echo "[INFO] /etc/fstab already has a mount for UUID=$UUID at $DATA_MOUNT"
fi

# Mount data filesystem
if ! mountpoint -q "$DATA_MOUNT"; then
  mount "$DATA_MOUNT" || { echo "[ERROR] Failed to mount $DATA_MOUNT" >&2; exit 1; }
  echo "[INFO] Mounted $DATA_MOUNT"
else
  echo "[INFO] $DATA_MOUNT is already mounted"
fi

# Add bind mount for Laravel storage
BIND_SRC="$DATA_MOUNT"
BIND_DST="$APP_PATH/storage/app/public"
FSTAB_LINE_BIND="$BIND_SRC $BIND_DST none bind 0 0"
if ! grep -qE "^$BIND_SRC\s+$BIND_DST\s+none\s+bind\s+" /etc/fstab; then
  echo "$FSTAB_LINE_BIND" >> /etc/fstab
  echo "[INFO] Added bind mount to /etc/fstab: $FSTAB_LINE_BIND"
else
  echo "[INFO] Bind mount already present in /etc/fstab"
fi

# Mount bind
if mountpoint -q "$BIND_DST"; then
  echo "[INFO] $BIND_DST already mounted"
else
  mount "$BIND_DST" || { echo "[ERROR] Failed to mount bind at $BIND_DST" >&2; exit 1; }
  echo "[INFO] Bind-mounted $BIND_SRC -> $BIND_DST"
fi

# Permissions
chown -R "$OWNER" "$BIND_DST"
chmod -R 775 "$APP_PATH/storage" || true
chmod -R 775 "$APP_PATH/bootstrap/cache" || true

echo "[INFO] Ensuring storage symlink exists..."
if [[ -f "$APP_PATH/artisan" && $RUN_ARTISAN -eq 1 ]]; then
  if command -v php >/dev/null 2>&1; then
    (cd "$APP_PATH" && php artisan storage:link || true)
  else
    echo "[WARN] php not found, skipping artisan storage:link"
  fi
else
  echo "[INFO] Skipping artisan step (either --skip-artisan or artisan not found)"
fi

echo "[SUCCESS] Storage provisioned. Summary:"
echo "  Device:       $DEVICE (UUID=$UUID)"
echo "  FS type:      ${EXISTING_FS:-$FS_TYPE}"
echo "  Data mount:   $DATA_MOUNT"
echo "  Bind mount:   $BIND_SRC -> $BIND_DST"
echo "  Owner set:    $OWNER"
