<template>
  <div>
    <div class="card mb-4">
      <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex flex-row bd-highlight mb-3">
          <input type="text" class="form-control me-2" placeholder="Search" v-model="searchQuery" @input="handleSearch()">
          <select class="form-select w-auto" aria-label="View" v-model="selectedView" @change="handleViewChange()">
            <option selected disabled>View</option>
            <option value="all">All</option>
            <option value="unique">Unique</option>
            <option value="duplicate">Duplicate</option>
          </select>
        </div>
        <div class="d-flex flex-row bd-highlight mb-3">
          <!-- Import Section -->
          <div class="d-flex align-items-center">
            <!-- Import Status Badge -->
            <span v-if="importStatus" class="badge me-1" :class="importStatusClass">
              {{ importStatus }}
            </span>
            <input 
              type="file" 
              ref="fileInput" 
              style="display: none" 
              accept=".csv,.xlsx,.txt"
              @change="handleFileSelect"
            >
            <button class="btn btn-outline-success me-2" @click="triggerFileInput">Import</button>
          </div>
          
          <!-- Export Section -->
          <select 
            class="form-select" 
            aria-label="Export" 
            v-model="selectedExportType"
            @change="handleExport"
            :disabled="exporting"
          >
            <option selected disabled>Export</option>
            <option value="all">All</option>
            <option value="unique">Unique</option>
            <option value="duplicate">Duplicate</option>
          </select>
        </div>
      </div>
      <div class="card-body">
        <DataTable :clients="clients" :pagination="pagination" @page-changed="fetchClients" :loading="isLoading" @edit-client="handleEditClient" />
      </div>
    </div>

    <!-- Import Modal Component -->
    <ImportModal 
      :show="showImportModal" 
      :import-data="importData"
      :refreshing-status="refreshingStatus"
      @close="closeImportModal"
      @refresh-status="checkImportStatus"
    />

    <!-- Edit Client Modal -->
    <EditClientModal 
      :show="showEditModal" 
      :client="selectedClient"
      :updating="updatingClient"
      @close="closeEditModal"
      @update-client="updateClient"
    />
  </div>
</template>

<script>
import DataTable from "../../components/DataTable.vue";
import ImportModal from "../../components/ImportModal.vue";
import EditClientModal from "../../components/EditClientModal.vue";
import { getClients, importClients, getImportStatus, exportClients, updateClient } from '@/services/client';

export default {
  components: {
    DataTable,
    ImportModal,
    EditClientModal
  },
  data() {
    return {
      clients: [],
      pagination: {
        current_page: 1,
        last_page: 1
      },
      selectedView: 'all',
      searchQuery: '',
      isLoading: false,
      searchTimeout: null,
      
      // Import related data
      selectedFile: null,
      importData: null,
      showImportModal: false,
      importStatus: '',
      refreshingStatus: false,
      
      // Export related data
      selectedExportType: 'Export',
      exporting: false,

      // Edit related data
      showEditModal: false,
      selectedClient: null,
      updatingClient: false
    };
  },
  computed: {
    importStatusClass() {
      switch(this.importStatus) {
        case 'Processing': return 'bg-warning';
        case 'Completed': return 'bg-success';
        case 'Failed': return 'bg-danger';
        case 'Queued': return 'bg-info';
        default: return 'bg-secondary';
      }
    }
  },
  created() {
    this.fetchClients();
  },
  methods: {
    fetchClients(page = 1) {
      this.isLoading = true;

      const params = {
        page,
        filter: this.selectedView,
        search: this.searchQuery || undefined
      };

      Object.keys(params).forEach(key => {
        if (params[key] === undefined) {
          delete params[key];
        }
      });

      getClients(params)
        .then((response) => {
          const data = response.data;

          if (data.code == 200) {
            this.clients = data.content.data;
            this.pagination = {
              current_page: data.content.current_page,
              last_page: data.content.last_page,
              total: data.content.total,
              per_page: data.content.per_page,
            };
          }
        })
        .catch(err => {
          console.error('Failed To List Clients:', err);
          this.clients = [];
          this.pagination = {
            current_page: 1,
            last_page: 1,
            total: 0,
            per_page: 10,
          };
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    handleViewChange() {
      this.pagination.current_page = 1;
      this.fetchClients(1);
    },
    handleSearch() {
      clearTimeout(this.searchTimeout);
      this.searchTimeout = setTimeout(() => {
        this.pagination.current_page = 1;
        this.fetchClients(1);
      }, 500);
    },

    // Import Methods
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    
    handleFileSelect(event) {
      const file = event.target.files[0];
      if (file) {
        this.selectedFile = file;
        this.uploadFile();
      }
    },
    
    uploadFile() {
      if (!this.selectedFile) return;
      
      this.importStatus = 'Processing';
      this.showImportModal = true;
      
      const formData = new FormData();
      formData.append('file', this.selectedFile);
      
      importClients(formData)
        .then(response => {
          const data = response.data;
          if (data.code === 200) {
            this.importData = data.content;
            this.importStatus = 'Queued';
          }
        })
        .catch(error => {
          console.error('Import failed:', error);
          this.importStatus = 'Failed';
          this.importData = {
            message: 'Import failed. Please check the file format and try again.',
            failures: error.response?.data?.failures || []
          };
        });
    },
    
    checkImportStatus() {
      const importId = this.importData?.import_id;
      if (!importId) return;
      
      this.refreshingStatus = true;
      
      getImportStatus(importId)
        .then(response => {
          const data = response.data;
          if (data.code === 200) {
            const statusData = data.content;
            this.importData = { ...this.importData, ...statusData };
            
            // Update status badge
            this.importStatus = statusData.status.charAt(0).toUpperCase() + statusData.status.slice(1);
          }
        })
        .catch(error => {
          console.error('Failed to check import status:', error);
        })
        .finally(() => {
          this.refreshingStatus = false;
        });
    },
    
    closeImportModal() {
      this.showImportModal = false;
      this.importData = null;
      this.importStatus = '';
      this.selectedFile = null;
      this.refreshingStatus = false;
      
      // Clear file input
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = '';
      }
      
      // Refresh client list when modal closes and show loader
      this.fetchClients();
    },

    // Export Methods
    handleExport(event) {
      const exportType = event.target.value;
      
      if (exportType === 'Export') return;
      
      this.exporting = true;
      
      exportClients(exportType)
        .then(response => {
          // Create a blob from the response data
          const blob = new Blob([response.data], { 
            type: response.headers['content-type'] 
          });
          
          // Create download link
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          
          // Get filename from content-disposition header or use default
          const contentDisposition = response.headers['content-disposition'];
          let filename = `clients_${exportType}_${new Date().toISOString().split('T')[0]}.csv`;
          
          if (contentDisposition) {
            const filenameMatch = contentDisposition.match(/filename="?(.+)"?/);
            if (filenameMatch && filenameMatch.length === 2) {
              filename = filenameMatch[1];
            }
          }
          
          link.setAttribute('download', filename);
          document.body.appendChild(link);
          link.click();
          link.remove();
          window.URL.revokeObjectURL(url);
        })
        .catch(error => {
          console.error('Export failed:', error);
          alert('Export failed. Please try again.');
        })
        .finally(() => {
          this.exporting = false;
          // Reset the select to the placeholder after export
          this.selectedExportType = 'Export';
        });
    },

    // Edit Methods
    handleEditClient(client) {
      this.selectedClient = client;
      this.showEditModal = true;
    },

    closeEditModal() {
      this.showEditModal = false;
      this.selectedClient = null;
      this.updatingClient = false;
    },

    updateClient(formData) {
      if (!this.selectedClient) return;
      this.updatingClient = true;

      updateClient(this.selectedClient.id, formData)
        .then(response => {
          const data = response.data;
          if (data.code === 200) {
            this.closeEditModal();
            this.fetchClients(this.pagination.current_page);
          }
        })
        .catch(error => {
          console.error('Update failed:', error);
          alert('Failed to update client. Please try again.');
        })
        .finally(() => {
          this.updatingClient = false;
        });
    }
  }
};
</script>

<style scoped>
.card-header {
  background-color: #f8f9fa;
}
</style>