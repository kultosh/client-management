<template>
    <div v-if="show" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" @click="$emit('close')"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete client <strong>"{{ client?.company_name }}"</strong>?</p>
                    <p class="text-muted">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
                    <button 
                        type="button" 
                        class="btn btn-danger" 
                        @click="$emit('confirm-delete')"
                        :disabled="deleting"
                    >
                        <span v-if="deleting" class="spinner-border spinner-border-sm me-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                        </span>
                        {{ deleting ? 'Deleting...' : 'Delete' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'DeleteConfirmationModal',
    props: {
        show: {
            type: Boolean,
            default: false
        },
        client: {
            type: Object,
            default: null
        },
        deleting: {
            type: Boolean,
            default: false
        }
    },
}
</script>

<style scoped>
.modal {
  background-color: rgba(0,0,0,0.5);
}
</style>