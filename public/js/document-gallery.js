class DocumentGallery {
    constructor() {
        this.currentIndex = 0;
        this.images = [];
        this.touchStartX = 0;
        this.touchEndX = 0;
        this.swipeThreshold = 50;
        
        this.init();
    }

    init() {
        this.setupModal();
        this.setupEventListeners();
        this.cacheImages();
    }

    setupModal() {
        // Create modal HTML
        this.modal = document.createElement('div');
        this.modal.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex flex-col items-center justify-center hidden';
        this.modal.innerHTML = `
            <div class="relative w-full h-full flex items-center justify-center">
                <button class="absolute top-4 right-4 text-white text-2xl z-10" onclick="document.gallery.closeModal()">&times;</button>
                <button class="absolute left-4 bg-white bg-opacity-30 text-white p-2 rounded-full z-10" id="prevBtn">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <div class="max-w-full max-h-full flex items-center justify-center p-4">
                    <img id="modalImage" class="max-h-[90vh] max-w-full object-contain" src="" alt="">
                </div>
                <button class="absolute right-4 bg-white bg-opacity-30 text-white p-2 rounded-full z-10" id="nextBtn">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
            <div class="absolute bottom-4 text-white text-center">
                <p id="imageInfo" class="text-sm"></p>
            </div>
        `;
        document.body.appendChild(this.modal);

        // Cache modal elements
        this.modalImage = this.modal.querySelector('#modalImage');
        this.imageInfo = this.modal.querySelector('#imageInfo');
    }

    cacheImages() {
        this.images = Array.from(document.querySelectorAll('.preview-image'));
    }

    setupEventListeners() {
        // Touch events for swipe
        this.modal.addEventListener('touchstart', (e) => {
            this.touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        this.modal.addEventListener('touchend', (e) => {
            this.touchEndX = e.changedTouches[0].screenX;
            this.handleSwipe();
        }, { passive: true });

        // Click events for navigation
        document.addEventListener('click', (e) => {
            const imageItem = e.target.closest('.preview-image');
            if (imageItem) {
                e.preventDefault();
                this.currentIndex = this.images.indexOf(imageItem);
                this.showCurrentImage();
                this.openModal();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!this.modal.classList.contains('hidden')) {
                if (e.key === 'ArrowLeft') {
                    this.showPrevImage();
                } else if (e.key === 'ArrowRight') {
                    this.showNextImage();
                } else if (e.key === 'Escape') {
                    this.closeModal();
                }
            }
        });

        // Navigation buttons
        this.modal.querySelector('#prevBtn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.showPrevImage();
        });

        this.modal.querySelector('#nextBtn')?.addEventListener('click', (e) => {
            e.stopPropagation();
            this.showNextImage();
        });

        // Close when clicking outside image
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.closeModal();
            }
        });
    }


    handleSwipe() {
        if (this.touchEndX < this.touchStartX - this.swipeThreshold) {
            this.showNextImage();
        }
        if (this.touchEndX > this.touchStartX + this.swipeThreshold) {
            this.showPrevImage();
        }
    }

    showCurrentImage() {
        if (!this.images[this.currentIndex]) return;
        
        const imageUrl = this.images[this.currentIndex].src;
        this.modalImage.src = imageUrl;
        this.imageInfo.textContent = `${this.currentIndex + 1} / ${this.images.length}`;
    }

    showNextImage() {
        this.currentIndex = (this.currentIndex + 1) % this.images.length;
        this.showCurrentImage();
    }

    showPrevImage() {
        this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
        this.showCurrentImage();
    }

    openModal() {
        this.showCurrentImage();
        this.modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    closeModal() {
        this.modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Initialize gallery when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Only initialize if there are images
    if (document.querySelector('.preview-image')) {
        document.gallery = new DocumentGallery();
    }
});
