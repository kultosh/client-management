<template>
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999">
        <div v-for="toast in toasts" :key="toast.id" class="toast show" :class="`toast-${toast.type}`" role="alert">
            <div class="toast-header">
                <strong class="me-auto">{{ toast.title }}</strong>
                <small>{{ toast.time }}</small>
                <button type="button" class="btn-close" @click="removeToast(toast.id)"></button>
            </div>
            <div class="toast-body">
                {{ toast.message }}
            </div>
        </div>
    </div>
</template>

<script>
export default {
  name: 'ToastNotification',
  props: {
    toasts: {
        type: Array,
        default: () => []
    }
  },
  methods: {
    removeToast(id) {
        this.$emit('remove-toast', id);
    }
  }
}
</script>

<style scoped>
.toast-success {
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.toast-success .toast-header {
    background-color: #d1e7dd;
    color: #0f5132;
}

.toast-error {
    background-color: #f8d7da;
    border-color: #f5c2c7;
}

.toast-error .toast-header {
    background-color: #f8d7da;
    color: #842029;
}
</style>