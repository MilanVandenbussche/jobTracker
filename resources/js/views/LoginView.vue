<template>
    <div class="col-12 col-md-6 offset-md-3 vh-100 d-flex flex-column position-relative">
        <div class="position-absolute top-0 start-50 translate-middle-x">
            <h1 class="my-5 text-center fw-semibold align-self-center fs-7xl">Job<span class="text-primary">Trackr</span></h1>
        </div>
        <div class="my-auto w-100">
            <h1 class="d-flex align-items-center">
                <span @click="register = false" class="cursor-pointer"
                      :class="[!register ? 'text-primary scale-100' : 'text-muted scale-80']">Login</span>
                <span @click="register = true" class="cursor-pointer"
                      :class="[register ? 'text-primary scale-100' : 'text-muted scale-80']">Register</span>
            </h1>
            <login v-if="!register" @register="register = !register"/>
            <register v-else @register="register = !register"/>
        </div>
    </div>
</template>
<script>
export default {
    name: 'Login View',
    data() {
        return {
            register: false,
            showPassword: false,
        }
    },
}
</script>
