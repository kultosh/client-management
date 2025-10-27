import axios from 'axios';

const API_URL = process.env.VUE_APP_ROOT_API + '/clients';

// GET all clients with optional pagination or filters
export function getClients(params = {}) {
  return axios.get(API_URL, { params });
}

// IMPORT clients (file upload)
export function importClients(formData) {
  return axios.post(`${API_URL}/import`, formData, {
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  });
}

// CHECK import status
export function getImportStatus(importId) {
  return axios.get(`${API_URL}/imports/${importId}/status`);
}

// EXPORT clients
export function exportClients(type = 'all') {
  return axios.get(`${API_URL}/export`, { 
    responseType: 'blob',
    params: { type }
  });
}

// UPDATE a client
export function updateClient(id, data) {
  return axios.put(`${API_URL}/${id}`, data);
}

// DELETE a client
export function deleteClient(id) {
  return axios.delete(`${API_URL}/${id}`);
}