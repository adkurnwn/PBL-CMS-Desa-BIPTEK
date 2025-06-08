<template>
    <div class="flex flex-col w-full text-left text-[#111827]">
        <!-- Avatar and Name Row -->
        <div
            class="flex flex-row items-center justify-start gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
            <div
                class="w-[30px] h-[30px] sm:w-[32px] sm:h-[32px] md:w-[35px] md:h-[35px] xl:w-[26px] xl:h-[26px] 2xl:w-[30px] 2xl:h-[30px] 3xl:w-[35px] 3xl:h-[35px] 4xl:w-[40px] 4xl:h-[40px] rounded-full overflow-hidden flex-shrink-0">
                <img :src="avatarUrl" :alt="userName" class="w-full h-full object-cover" />
            </div>
            <div>
                <div
                    class="flex-1 relative font-medium text-[9px] sm:text-[10px] md:text-[11px] lg:text-[12px] xl:text-[13px] 2xl:text-[15px] 3xl:text-[15px] 4xl:text-[18px]">
                    {{ userName }}
                </div>
                <div
                    class="flex-1 relative italic text-menuText text-[7px] sm:text-[8px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[11px] 4xl:text-[14px]">
                    {{ emailUser }}
                </div>
            </div>
        </div>

        <div
            class="flex w-full gap-[7px] sm:gap-[7px] md:gap-[7px] xl:gap-[7px] 2xl:gap-[8px] 3xl:gap-[9px] 4xl:gap-[10px]">
            <div
                class="w-[30px] h-[30px] sm:w-[32px] sm:h-[32px] md:w-[35px] md:h-[35px] xl:w-[26px] xl:h-[26px] 2xl:w-[30px] 2xl:h-[30px] 3xl:w-[35px] 3xl:h-[35px] 4xl:w-[40px] 4xl:h-[40px] rounded-full overflow-hidden flex-shrink-0">
                <!-- Empty space to align with avatar above -->
            </div>
            <div
                class="w-full overflow-hidden flex flex-col items-start justify-center text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] xl:text-[12px] 2xl:text-[13px] 3xl:text-[15px] 4xl:text-[17px]">
                <!-- News Title Reference -->
                <div v-if="newsTitle" class="self-stretch relative">
                    <div
                        class="text-[7px] sm:text-[8px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[11px] 4xl:text-[14px] text-menuText">
                        Komentar pada: <span class="italic font-bold">{{ newsTitle }}</span>
                    </div>
                </div>

                <!-- Parent Comment Reference (if exists) -->
                <div v-if="parentComment" class="self-stretch relative">
                    <div
                        class="italic p-[2.6px] sm:p-[3px] md:p-[3.4px] lg:p-[3.6px] xl:p-[4px] 2xl:p-[4.6px] 3xl:p-[5.4px] 4xl:p-[6px] rounded-r-sm w-full sm:rounded-r-sm md:rounded-r lg:rounded-r-md xl:rounded-r-lg 2xl:rounded-r-xl 3xl:rounded-r-2xl 4xl:rounded-r-md bg-gray-200 bg-opacity-50 border-l border-[#6f7684]">
                        <div
                            class="text-[7px] sm:text-[8px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[11px] 4xl:text-[14px] font-thin text-gray-600">
                            {{ parentCommentUsername }}
                        </div>
                        <div
                            class="text-[7px] sm:text-[8px] md:text-[8px] lg:text-[9px] xl:text-[10px] 2xl:text-[11px] 3xl:text-[11px] 4xl:text-[14px] text-gray-700 line-clamp-2">
                            {{ parentComment }}
                        </div>
                    </div>
                </div>

                <!-- Main Comment Content -->
                <div class="self-stretch relative font-normal line-clamp-3 overflow-hidden mb-2">
                    {{ comment }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    userName: {
        type: String,
        default: ''
    },
    emailUser: {
        type: String,
        default: ''
    },
    comment: {
        type: String,
        default: ''
    },
    parentComment: {
        type: String,
        default: null
    },
    parentCommentUsername: {
        type: String,
        default: null
    },
    newsTitle: {
        type: String,
        default: null
    }
})

const getAvatar = (name) => {
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(
        name
    )}&background=random`;
};

const avatarUrl = computed(() => {
    return getAvatar(props.userName)
})

const handleImageError = (event) => {
    // Fallback if image fails to load
    event.target.src = getAvatar(props.userName);
}
</script>
