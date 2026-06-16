<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    icon: {
        type: String,
        default: ''
    }
});

const classes = computed(() =>
    props.active
        ? 'flex items-center px-4 py-3 mt-1 text-sm font-bold bg-white/10 text-yellow-400 border-l-4 border-yellow-400 transition-colors duration-200'
        : 'flex items-center px-4 py-3 mt-1 text-sm font-medium text-blue-100 hover:bg-white/5 hover:text-white border-l-4 border-transparent transition-colors duration-200'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <span class="w-5 h-5 mr-3 flex items-center justify-center shrink-0" v-html="icon" v-if="icon"></span>
        <slot />
    </Link>
</template>
