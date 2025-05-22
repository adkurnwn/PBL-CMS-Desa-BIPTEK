<template>
    <button class="w-[21px] h-[21px] flex items-center justify-center hover:opacity-80 transition-opacity">
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
    </button>
</template>

<script setup>
defineProps({
    type: {
        type: String,
        required: true,
        validator: (value) => ['edit', 'delete'].includes(value)
    }
});
</script>
