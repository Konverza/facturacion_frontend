<div id="loader" class="fixed z-[300] hidden">
    <div
        class="fixed z-[300] flex h-screen w-full flex-col items-center justify-center gap-4 bg-gray-200/70 dark:bg-gray-950/90">
        <svg viewBox="0 0 240 240" height="240" width="240" class="loader">
            <circle stroke-linecap="round" stroke-dashoffset="-330" stroke-dasharray="0 660" stroke-width="20"
                stroke="#000" fill="none" r="105" cy="120" cx="120" class="loader-ring loader-ring-a">
            </circle>
            <circle stroke-linecap="round" stroke-dashoffset="-110" stroke-dasharray="0 220" stroke-width="20"
                stroke="#000" fill="none" r="35" cy="120" cx="120" class="loader-ring loader-ring-b">
            </circle>
            <circle stroke-linecap="round" stroke-dasharray="0 440" stroke-width="20" stroke="#000" fill="none"
                r="70" cy="120" cx="85" class="loader-ring loader-ring-c"></circle>
            <circle stroke-linecap="round" stroke-dasharray="0 440" stroke-width="20" stroke="#000" fill="none"
                r="70" cy="120" cx="155" class="loader-ring loader-ring-d"></circle>
        </svg>
        <div class="text-center text-xl font-semibold text-gray-700 dark:text-gray-300">
            Cargando...
        </div>
    </div>
</div>
