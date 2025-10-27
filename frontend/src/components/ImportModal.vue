<template>
  <div v-if="show" class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Import Status</h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body">
          <div v-if="importData">
            <p><strong>Import ID:</strong> {{ importData.import_id }}</p>
            <p><strong>Status:</strong> {{ importData.status || 'queued' }}</p>
            <p>{{ importData.message }}</p>
            
            <!-- Failures List -->
            <div v-if="importData.failures && importData.failures.length" class="mt-3">
              <h6>Import Failures ({{ importData.failures.length }}):</h6>
              <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                <table class="table table-sm table-bordered">
                  <thead>
                    <tr>
                      <th>Row</th>
                      <th>Attribute</th>
                      <th>Error</th>
                      <th>Value</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(failure, index) in importData.failures" :key="index">
                      <td>{{ failure.row }}</td>
                      <td>{{ failure.attribute }}</td>
                      <td class="text-danger">{{ failure.errors.join(', ') }}</td>
                      <td>{{ failure.values }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div v-else class="text-center">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Processing import...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Close</button>
          <button 
            v-if="importData && importData.import_id" 
            type="button" 
            class="btn btn-primary" 
            @click="$emit('refresh-status')"
            :disabled="refreshingStatus"
          >
            <span v-if="refreshingStatus" class="spinner-border spinner-border-sm me-2" role="status">
              <span class="visually-hidden">Loading...</span>
            </span>
            {{ refreshingStatus ? 'Refreshing...' : 'Refresh Status' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ImportModal',
  props: {
    show: {
      type: Boolean,
      default: false
    },
    importData: {
      type: Object,
      default: null
    },
    refreshingStatus: {
      type: Boolean,
      default: false
    }
  },
  emits: ['close', 'refresh-status']
}
</script>

<style scoped>
.modal {
  background-color: rgba(0,0,0,0.5);
}
</style>