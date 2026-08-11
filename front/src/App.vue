<!-- src/App.vue -->

<script setup>
import { useRoute } from 'vue-router';
import PublicLayout from '@/layouts/PublicLayout.vue';
import AdminLayout from '@/layouts/AdminLayout.vue';

const route = useRoute();
</script>

<template>
    <PublicLayout v-if="route.meta.layout === 'public'" />
    <AdminLayout v-else-if="route.meta.layout === 'admin'" />
    <router-view v-else />
</template>

<style lang="scss">
@use '@/assets/styles/variables' as *;

*,
*::before,
*::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    font-size: 16px;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body {
    font-family: $font-family-base;
    font-size: $font-size-base;
    color: $color-text;
    background-color: $color-bg;
    line-height: 1.6;
}

a {
    color: $color-secondary;
    text-decoration: none;

    &:hover {
        color: $color-accent;
    }
}

img {
    max-width: 100%;
    height: auto;
}

button {
    font-family: $font-family-base;
}
</style>