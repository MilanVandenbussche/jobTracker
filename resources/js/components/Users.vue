<template>
<h1 class="fs-6xl">Users</h1>
    <div class="row">
        <users-component @updateAuthUser="this.$emit('updateAuthUser')"></users-component>
    </div>
</template>
<script>
export default {
    name:'dashboard',
    emits: ['updateAuthUser'],
    data() {
        return {
            //
        }
    },
    methods: {
        //
    },
}
</script>
