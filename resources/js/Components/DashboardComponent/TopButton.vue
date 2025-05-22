<template>
    <button
        class="inline-flex items-center justify-center h-[40px] rounded-[10px] bg-selectedText px-[19px] max-sm:px-[11px] py-[11px] text-[15px] font-medium text-white transition duration-150 ease-in-out hover:bg-opacity-90 active:bg-opacity-80"
    >
        <!-- Fixed SVG icon -->
        <span class="w-5 h-5 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                <mask id="mask0_1642_3445" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
                    width="21" height="20">
                    <rect x="0.5" width="20" height="20" fill="#D9D9D9" />
                </mask>
                <g mask="url(#mask0_1642_3445)">
                    <path
                        d="M9.6665 10.8337H4.6665V9.16699H9.6665V4.16699H11.3332V9.16699H16.3332V10.8337H11.3332V15.8337H9.6665V10.8337Z"
                        fill="white" />
                </g>
            </svg>
        </span>
        <!-- Fixed "Tambah" text, hidden on small screens -->
        <span class="ml-2 max-sm:hidden">Tambah</span>
    </button>
</template>

<script setup>
// No props needed anymore as we've fixed the icon and text
</script>
