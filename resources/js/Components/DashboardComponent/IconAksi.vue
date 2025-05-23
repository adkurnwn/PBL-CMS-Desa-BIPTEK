<template>
    <button
        class="itemsw-[13px] sm:w-[13px] md:w-[13px] xl:w-[13px] 2xl:w-[15px] 3xl:w-[18px] 4xl:w-[20px] 
                     h-[13px] sm:h-[13px] md:h-[13px] xl:h-[13px] 2xl:h-[15px] 3xl:h-[18px] 4xl:h-[20px] flex items-center justify-center hover:opacity-80 transition-opacity">
        <!-- Edit Icon -->
        <svg v-if="type === 'edit'" xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22"
            fill="none">
            <mask id="mask0_1659_4389" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="21"
                height="22">
                <rect y="0.5" width="21" height="21" fill="#D9D9D9" />
            </mask>
            <g mask="url(#mask0_1659_4389)">
                <path
                    d="M1.75 21.5V18H19.25V21.5H1.75ZM5.25 14.5H6.475L13.3 7.69688L12.0531 6.45L5.25 13.275V14.5ZM3.5 16.25V12.5312L13.3 2.75313C13.4604 2.59271 13.6464 2.46875 13.8578 2.38125C14.0693 2.29375 14.2917 2.25 14.525 2.25C14.7583 2.25 14.9844 2.29375 15.2031 2.38125C15.4219 2.46875 15.6188 2.6 15.7938 2.775L16.9969 4C17.1719 4.16042 17.2995 4.35 17.3797 4.56875C17.4599 4.7875 17.5 5.01354 17.5 5.24688C17.5 5.46563 17.4599 5.68073 17.3797 5.89219C17.2995 6.10365 17.1719 6.29688 16.9969 6.47188L7.21875 16.25H3.5Z"
                    fill="#FFC107" />
            </g>
        </svg>

        <!-- Delete Icon -->
        <svg v-else-if="type === 'delete'" xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21"
            fill="none">
            <mask id="mask0_1692_3452" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="22"
                height="21">
                <rect x="0.5625" width="21" height="21" fill="#D9D9D9" />
            </mask>
            <g mask="url(#mask0_1692_3452)">
                <path
                    d="M6.6875 18.375C6.20625 18.375 5.79427 18.2036 5.45156 17.8609C5.10885 17.5182 4.9375 17.1062 4.9375 16.625V5.25H4.0625V3.5H8.4375V2.625H13.6875V3.5H18.0625V5.25H17.1875V16.625C17.1875 17.1062 17.0161 17.5182 16.6734 17.8609C16.3307 18.2036 15.9187 18.375 15.4375 18.375H6.6875ZM15.4375 5.25H6.6875V16.625H15.4375V5.25ZM8.4375 14.875H10.1875V7H8.4375V14.875ZM11.9375 14.875H13.6875V7H11.9375V14.875Z"
                    fill="#C23D39" />
            </g>
        </svg>

        <!-- archive Icon -->
        <svg v-else-if="type === 'archive'" xmlns="http://www.w3.org/2000/svg" width="22" height="21"
            viewBox="0 0 22 21" fill="none">
            <mask id="mask0_1692_3452" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="22"
                height="21">
                <rect x="0.5625" width="21" height="21" fill="#D9D9D9" />
            </mask>
            <g mask="url(#mask0_1692_3452)">
                <path
                    d="M8.0625 13.25L11.5625 9.75L10.3375 8.525L8.9375 9.925V6.25H7.1875V9.925L5.7875 8.525L4.5625 9.75L8.0625 13.25ZM1.9375 4.5V14.125H14.1875V4.5H1.9375ZM1.9375 15.875C1.45625 15.875 1.04427 15.7036 0.701563 15.3609C0.358854 15.0182 0.1875 14.6062 0.1875 14.125V3.20938C0.1875 3.00521 0.220313 2.80833 0.285938 2.61875C0.351562 2.42917 0.45 2.25417 0.58125 2.09375L1.675 0.759375C1.83542 0.555208 2.03594 0.398438 2.27656 0.289062C2.51719 0.179688 2.76875 0.125 3.03125 0.125H13.0938C13.3562 0.125 13.6078 0.179688 13.8484 0.289062C14.0891 0.398438 14.2896 0.555208 14.45 0.759375L15.5438 2.09375C15.675 2.25417 15.7734 2.42917 15.8391 2.61875C15.9047 2.80833 15.9375 3.00521 15.9375 3.20938V14.125C15.9375 14.6062 15.7661 15.0182 15.4234 15.3609C15.0807 15.7036 14.6687 15.875 14.1875 15.875H1.9375ZM2.2875 2.75H13.8375L13.0938 1.875H3.03125L2.2875 2.75Z"
                    fill="#6C757D" />
            </g>
        </svg>

        <!-- restore icon - updated -->
        <svg v-else-if="type === 'restore'" xmlns="http://www.w3.org/2000/svg" height="100%" viewBox="0 -960 960 960" width="100%" fill="#4C7660">
            <path d="M440-320h80v-166l64 62 56-56-160-160-160 160 56 56 64-62v166ZM280-120q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120H280Zm400-600H280v520h400v-520Zm-400 0v520-520Z"/>
        </svg>
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['edit', 'delete', 'archive', 'restore'].includes(value)
    }
});
</script>
