<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import '@mdi/font/css/materialdesignicons.css';
import EditFormModal from '@/components/modals/EditFormModal.vue';
import PreviewFormModal from '@/components/modals/PreviewFormModal.vue';
import axios from 'axios';
import Swal from 'sweetalert2';


import {
  EditOutlined,
  EyeOutlined,
  DeleteOutlined,
  PlusOutlined
} from '@ant-design/icons-vue';

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
    const apiForms = Array.isArray(response.data)
      ? response.data
      : response.data.data;

    if (!Array.isArray(apiForms)) {
      throw new Error('API did not return an array.');
    }

    forms.value = apiForms.map((form: any) => ({
      title: form.name,
      description: form.description,
      fields: form.fields || [],
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
  label.trim().toLowerCase().replace(/\s+/g, '_').replace(/[^\w_]/g, '');

const saving = ref(false);
const saveForm = async () => {
  if (!newForm.value.title.trim()) {
    await Swal.fire({
      icon: 'warning',
      title: 'Missing Title',
      text: 'Form title is required!',
    });
    return;
  }

  if (!newForm.value.description.trim()) {
    await Swal.fire({
      icon: 'warning',
      title: 'Missing Description',
      text: 'Form description is required!',
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
        type:
          field.type === 'text' && field.label.toLowerCase().includes('email')
            ? 'email'
            : field.type,
        name: generateFieldName(field.label),
        required: field.required,
      })),
    };

    await axios.post(`${import.meta.env.VITE_API_BASE_URL}/api/forms`, payload);
    showModal.value = false;
    // Show SweetAlert and wait for user to confirm
    await Swal.fire({
      icon: 'success',
      title: 'Form Saved!',
      text: 'Your form has been successfully created.',
      confirmButtonText: 'OK',
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
      text: 'Error saving form. Check console for details.',
    });
  } finally {
    saving.value = false;
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
    fields: Array.isArray(src.fields) ? src.fields : []
  };
  showEditModal.value = true;
};

const saveEdit = (updated: any) => {
  if (editIndex.value !== null) {
    forms.value[editIndex.value] = updated;
  }
};

const showPreviewModal = ref(false);
const previewFormData = ref<{ title: string; fields: any[] }>({ title: '', fields: [] });

const openPreview = (idx: number) => {
  const src: any = forms.value[idx] || {};
  previewFormData.value = {
    title: src.title || 'Untitled Form',
    fields: JSON.parse(JSON.stringify(Array.isArray(src.fields) ? src.fields : [])),
  };
  showPreviewModal.value = true;
};
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-3">
    <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs" />

    <v-btn color="primary" class="small-btn" @click="openModal">
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

          <v-btn variant="tonal" size="small" color="error" class="small-btn">
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

  <!-- Create Form Modal -->
  <v-dialog v-model="showModal" width="600">
    <v-card>
      <v-card-title>Create New Form</v-card-title>
      <v-card-text>
        <label class="label">Form Title</label>
        <input class="input" v-model="newForm.title" placeholder="Enter form title" />

        <label class="label">Form Description</label>
        <input class="input" v-model="newForm.description" placeholder="Enter form description" />

        <div class="toolbar" style="margin: 10px 0">
          <span class="small">Add Fields:</span>
          <button class="btn" @click="addField('text')">+ Text Input</button>
          <button class="btn" @click="addField('textarea')">+ Text Area</button>
          <button class="btn" @click="addField('checkbox')">+ Checkbox</button>
          <button class="btn" @click="addField('radio')">+ Radio</button>
          <button class="btn" @click="addField('email')">+ Email</button>
        </div>

        <div v-if="newForm.fields.length === 0" class="empty">No fields yet. Add one above.</div>

        <div class="list">
          <div v-for="(f, i) in newForm.fields" :key="f.id" class="field-item">
            <div class="row" style="justify-content: space-between; margin-bottom: 8px;">
              <div class="row" style="gap: 10px">
                <span class="badge">{{ f.type }}</span>
                <label class="row small" style="gap: 6px;">
                  <input type="checkbox" v-model="f.required" /> required
                </label>
              </div>
              <div class="actions">
                <button class="btn" @click="move(i, -1)">↑</button>
                <button class="btn" @click="move(i, 1)">↓</button>
                <button class="btn danger" @click="removeField(i)">Delete</button>
              </div>
            </div>

            <label class="label">Label</label>
            <input class="input" v-model="f.label" placeholder="Enter field label" />

            <template v-if="f.type === 'checkbox' || f.type === 'radio'">
              <div class="hr"></div>
              <label class="label">Options</label>
              <div v-for="(op, oi) in f.options" :key="oi" class="row" style="gap: 10px;">
                <input class="input" v-model="f.options[oi]" />
                <button class="btn" @click="removeOption(f, oi)">🗑</button>
              </div>
              <div style="margin-top: 8px;">
                <a class="link" @click.prevent="addOption(f)">+ Add option</a>
              </div>
            </template>
          </div>
        </div>
      </v-card-text>
      <v-card-actions class="justify-end">
        <v-btn text @click="cancel">Cancel</v-btn>
        <v-btn color="primary" @click="saveForm" :disabled="saving">
  {{ saving ? 'Saving...' : 'Save Form' }}
</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <EditFormModal v-model="showEditModal" :formData="editFormData" @save="saveEdit" />

  <!-- Preview Modal Component -->
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
