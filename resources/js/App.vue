<!--
 * App.vue
 *
 * Main component
 * @author     Filip Caladi (xcalad01)
 * @license    MIT
 -->

<template>
    <div>
        <router-view :key="$route.fullPath"></router-view>
    </div>
</template>

<script>
    export default {};
</script>

<style lang="scss"></style>
