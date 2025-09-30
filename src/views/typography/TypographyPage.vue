<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import '@mdi/font/css/materialdesignicons.css';
import EditFormModal from '@/components/modals/EditFormModal.vue';
import PreviewFormModal from '@/components/modals/PreviewFormModal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';

import { EditOutlined, EyeOutlined, DeleteOutlined, PlusOutlined } from '@ant-design/icons-vue';

const router = useRouter();

const goToColors = () => {
  router.push('/colors'); // relative route
};

const page = ref({ title: 'Forms' });
const breadcrumbs = ref([{ title: 'Forms', disabled: true, href: '#' }]);

const forms = ref<any[]>([]);
const loading = ref(true);

const fetchForms = async () => {
  try {
    loading.value = true;
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/api/forms`);

    console.log('API Response:', response.data);

    // Support both wrapped and unwrapped API responses
    const apiForms = Array.isArray(response.data) ? response.data : response.data.data;

    if (!Array.isArray(apiForms)) {
      throw new Error('API did not return an array.');
    }

    forms.value = apiForms.map((form: any) => ({
      id: form.id,
      title: form.name,
      description: form.description,
      fields: form.fields || []
    }));
  } catch (error: any) {
    console.error('Error fetching forms:', error.response?.data || error.message);
    alert('Failed to load forms. Check console for details.');
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchForms();
});

const previewCount = (form: any) => form.fields?.length ?? 0;

const showModal = ref(false);

const newForm = ref({
  title: '',
  description: '',
  fields: [] as any[]
});

const genId = () => Date.now() + Math.random();

const openModal = () => {
  newForm.value = { title: '', description: '', fields: [] };
  showModal.value = true;
};

const cancel = () => {
  showModal.value = false;
};

const generateFieldName = (label: string) =>
  label
    .trim()
    .toLowerCase()
    .replace(/\s+/g, '_')
    .replace(/[^\w_]/g, '');

const saving = ref(false);
const saveForm = async () => {
  if (!newForm.value.title.trim()) {
    await Swal.fire({
      icon: 'warning',
      title: 'Missing Title',
      text: 'Form title is required!'
    });
    return;
  }

  if (!newForm.value.description.trim()) {
    await Swal.fire({
      icon: 'warning',
      title: 'Missing Description',
      text: 'Form description is required!'
    });
    return;
  }

  try {
    saving.value = true;

    const payload = {
      name: newForm.value.title.trim(),
      description: newForm.value.description.trim(),
      fields: newForm.value.fields.map((field) => ({
        label: field.label.trim(),
        type: field.type === 'text' && field.label.toLowerCase().includes('email') ? 'email' : field.type,
        name: generateFieldName(field.label),
        required: field.required
      }))
    };

    await axios.post(`${import.meta.env.VITE_API_BASE_URL}/api/forms`, payload);
    showModal.value = false;
    // Show SweetAlert and wait for user to confirm
    await Swal.fire({
      icon: 'success',
      title: 'Form Saved!',
      text: 'Your form has been successfully created.',
      confirmButtonText: 'OK'
      // Optionally add:
      // allowOutsideClick: false,
      // allowEscapeKey: false,
    });

    // Wait for Vue to finish any pending DOM updates
    await nextTick();

    // Now close the modal and reset form

    newForm.value = { title: '', description: '', fields: [] };

    await fetchForms();
  } catch (error: any) {
    console.error('Error saving form:', error.response?.data || error.message);
    await Swal.fire({
      icon: 'error',
      title: 'Save Failed',
      text: 'Error saving form. Check console for details.'
    });
  } finally {
    saving.value = false;
  }
};

const confirmDelete = async (index: number) => {
  const form = forms.value[index];
  const formId = form.id;
  console.log('formId', formId);
  const result = await Swal.fire({
    title: 'Are you sure?',
    text: `Do you really want to delete "${form.title}"? This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Yes, delete it!'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`${import.meta.env.VITE_API_BASE_URL}/api/forms/${formId}`);

      // Remove from local list
      forms.value.splice(index, 1);

      await Swal.fire({
        title: 'Deleted!',
        text: 'Form has been deleted.',
        icon: 'success'
      });
    } catch (error: any) {
      console.error('Delete error:', error.response?.data || error.message);
      await Swal.fire({
        title: 'Failed!',
        text: 'Could not delete the form. Check console for details.',
        icon: 'error'
      });
    }
  }
};

const addField = (type: string) => {
  const field = {
    id: genId(),
    type,
    label: '',
    required: false,
    options: type === 'checkbox' || type === 'radio' ? [''] : []
  };
  newForm.value.fields.push(field);
};

const removeField = (i: number) => {
  newForm.value.fields.splice(i, 1);
};

const move = (i: number, direction: number) => {
  const newIndex = i + direction;
  if (newIndex < 0 || newIndex >= newForm.value.fields.length) return;
  const temp = newForm.value.fields[i];
  newForm.value.fields[i] = newForm.value.fields[newIndex];
  newForm.value.fields[newIndex] = temp;
};

const addOption = (field: any) => field.options.push('');
const removeOption = (field: any, index: number) => field.options.splice(index, 1);

const showEditModal = ref(false);
const editIndex = ref<number | null>(null);
const editFormData = ref<{ title: string; fields: any[] }>({ title: '', fields: [] });

const openEdit = (idx: number) => {
  editIndex.value = idx;
  const src: any = forms.value[idx] || {};
  editFormData.value = {
    title: src.title || '',
    description: src.description || '',
    fields: Array.isArray(src.fields) ? src.fields : []
  };
  showEditModal.value = true;
};

const saveEdit = async (updated: any) => {
  if (editIndex.value === null) return;

  const id = forms.value[editIndex.value].id;

  try {
    await axios.put(`${import.meta.env.VITE_API_BASE_URL}/api/forms/${id}`, {
      name: updated.title,
      description: updated.description,
      fields: updated.fields
    });

    await Swal.fire({
      icon: 'success',
      title: 'Form Updated',
      text: 'Changes have been saved successfully.'
    });

    await fetchForms();
  } catch (error: any) {
    console.error('Update error:', error.response?.data || error.message);
    await Swal.fire({
      icon: 'error',
      title: 'Update Failed',
      text: 'There was a problem updating the form.'
    });
  }
};

const showPreviewModal = ref(false);
const previewFormData = ref<{ title: string; fields: any[] }>({ title: '', fields: [] });

const openPreview = (idx: number) => {
  const src: any = forms.value[idx] || {};
  previewFormData.value = {
    title: src.title || 'Untitled Form',
    fields: JSON.parse(JSON.stringify(Array.isArray(src.fields) ? src.fields : []))
  };
  showPreviewModal.value = true;
};
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-3">
    <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs" />

    <v-btn color="primary" class="small-btn" @click="goToColors">
      <template #prepend>
        <v-icon size="18"><PlusOutlined /></v-icon>
      </template>
      Create Form
    </v-btn>
  </div>

  <v-row v-if="!loading">
    <v-col v-for="(form, i) in forms" :key="i" cols="12" md="4">
      <UiParentCard :title="form.title" class="small-card">
        <div class="d-flex align-center mt-1 card-actions">
          <v-btn variant="tonal" size="small" color="primary" class="small-btn" @click="openEdit(i)">
            <template #prepend><EditOutlined /></template>
            Edit
          </v-btn>

          <v-btn variant="tonal" size="small" color="#020503ff" class="small-btn" @click="openPreview(i)">
            <template #prepend><EyeOutlined /></template>
            Preview
          </v-btn>

          <v-btn variant="tonal" size="small" color="#9b7714ff" class="small-btn">
            {{ previewCount(form) }}
          </v-btn>

          <v-btn variant="tonal" size="small" color="error" class="small-btn" @click="confirmDelete(i)">
            <template #prepend><DeleteOutlined /></template>
            Delete
          </v-btn>
        </div>
      </UiParentCard>
    </v-col>
  </v-row>

  <div v-else>
    <p>Loading forms...</p>
  </div>

  <EditFormModal v-model="showEditModal" :formData="editFormData" @save="saveEdit" />

  <PreviewFormModal v-model="showPreviewModal" :formData="previewFormData" />
</template>

<style scoped>
.small-card {
  padding: 12px !important;
  font-size: 14px;
}
.small-btn {
  min-height: 28px !important;
  font-size: 13px !important;
  padding: 0 10px !important;
}
.card-actions {
  gap: 25px;
}
.label {
  font-weight: bold;
  display: block;
  margin-bottom: 4px;
}
.input {
  width: 100%;
  padding: 6px 10px;
  margin-bottom: 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
}
.btn {
  padding: 4px 10px;
  margin: 2px;
  border: 1px solid #ccc;
  background: #f3f4f6;
  border-radius: 4px;
  font-size: 13px;
  cursor: pointer;
}
.btn.danger {
  color: #b91c1c;
  border-color: #fecaca;
}
.row {
  display: flex;
  align-items: center;
}
.badge {
  background-color: #e2e8f0;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 12px;
}
.link {
  color: #3b82f6;
  cursor: pointer;
  font-size: 13px;
}
.empty {
  color: #999;
  font-style: italic;
  margin-bottom: 12px;
}
.card-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: flex-start;
}
@media (min-width: 1024px) {
  .card-actions {
    justify-content: space-between;
    gap: 20px;
  }
}
.small-btn {
  min-height: 32px !important;
  font-size: 13px !important;
  padding: 4px 14px !important;
  flex: 1 1 auto;
  max-width: 150px;
}
</style>
