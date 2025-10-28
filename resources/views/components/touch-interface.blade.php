{{-- 
    Touch Interface Component
    Ky komponent ofron elemente të ndërfaqes së përdoruesit të optimizuara për ekranet me prekje.
    Përdorimi: <x-touch-interface />
--}}

@props(['enableSwipeNavigation' => true, 'enableTouchFeedback' => true])

<div id="touch-interface-container" {{ $attributes }}>
    {{-- Këtu mund të shtohen elemente shtesë specifike për ndërfaqen me prekje --}}
    <div class="touch-controls" style="display: none;">
        {{-- Kontrollet e navigimit për prekje --}}
        <div class="touch-navigation-hint">
            <div class="swipe-hint-left">
                <i class="fas fa-chevron-left"></i>
                <span>Rrëshqit majtas</span>
            </div>
            <div class="swipe-hint-right">
                <span>Rrëshqit djathtas</span>
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    </div>
    
    {{ $slot ?? '' }}
</div>

{{-- Stilet për komponentët e prekjes --}}
<style>
    .touch-navigation-hint {
        position: fixed;
        bottom: 20px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: space-between;
        padding: 0 20px;
        pointer-events: none;
        opacity: 0.7;
        z-index: 1000;
        font-size: 14px;
    }
    
    .swipe-hint-left, .swipe-hint-right {
        display: flex;
        align-items: center;
        background-color: rgba(0, 0, 0, 0.6);
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
    }
    
    .swipe-hint-left i {
        margin-right: 8px;
    }
    
    .swipe-hint-right i {
        margin-left: 8px;
    }
    
    /* Fshih ndihmat e navigimit pas 5 sekondash */
    .touch-navigation-hint {
        animation: fadeOut 0.5s ease-in-out 5s forwards;
    }
    
    @keyframes fadeOut {
        from { opacity: 0.7; }
        to { opacity: 0; }
    }
</style>
