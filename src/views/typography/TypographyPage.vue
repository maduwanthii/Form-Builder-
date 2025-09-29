<script setup lang="ts">
import { ref } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import '@mdi/font/css/materialdesignicons.css';
import EditFormModal from '@/components/modals/EditFormModal.vue';
import PreviewFormModal from '@/components/modals/PreviewFormModal.vue';

import {
  EditOutlined,
  EyeOutlined,
  DeleteOutlined,
  PlusOutlined
} from '@ant-design/icons-vue';

const page = ref({ title: 'Forms' });
const breadcrumbs = ref([{ title: 'Forms', disabled: true, href: '#' }]);

// ===== forms dummy data =====
const forms = ref([
  {
    title: 'Contact Form',
    fields: [
      { id: 1, type: 'text', label: 'Name', required: true, options: [] },
      { id: 2, type: 'text', label: 'Email', required: true, options: [] }
    ]
  },
  {
    title: 'Event Registration',
    fields: [
      { id: 3, type: 'text', label: 'Full Name', required: true, options: [] },
      { id: 4, type: 'radio', label: 'Ticket Type', required: true, options: ['Regular', 'VIP'] }
    ]
  },
  {
    title: 'Product Feedback',
    fields: [
      { id: 5, type: 'text', label: 'Product Name', required: true, options: [] },
      { id: 6, type: 'textarea', label: 'Comments', required: false, options: [] }
    ]
  }
]);

// === helpers ===
const previewCount = (form:any) => form.fields?.length ?? 0;

// ==== Create Modal Logic ====
const showModal = ref(false);

const newForm = ref({
  title: '',
  fields: [] as any[]
});

const genId = () => Date.now() + Math.random();

const openModal = () => {
  newForm.value = { title: '', fields: [] };
  showModal.value = true;
};

const cancel = () => {
  showModal.value = false;
};

const saveForm = () => {
  if (!newForm.value.title.trim()) return;
  forms.value.push({
    title: newForm.value.title,
    fields: JSON.parse(JSON.stringify(newForm.value.fields))
  });
  showModal.value = false;
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

// ==== Edit Modal Logic ====
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
    // deep clone to avoid accidental edits during preview
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

  <v-row>
    <v-col v-for="(form, i) in forms" :key="i" cols="12" md="4">
      <UiParentCard :title="form.title" class="small-card">
        <div class="d-flex align-center mt-1 card-actions">
          <v-btn
            variant="tonal"
            size="small"
            color="primary"
            class="small-btn"
            @click="openEdit(i)"
          >
            <template #prepend><EditOutlined /></template>
            Edit
          </v-btn>

        <v-btn
  variant="tonal"
  size="small"
  color="#020503ff"
  class="small-btn"
  @click="openPreview(i)"
>
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

  <!-- Create Form Modal -->
  <v-dialog v-model="showModal" width="600">
    <v-card>
      <v-card-title>Create New Form</v-card-title>
      <v-card-text>
        <label class="label">Form Title</label>
        <input class="input" v-model="newForm.title" placeholder="Enter form title" />

        <div class="toolbar" style="margin: 10px 0">
          <span class="small">Add Fields:</span>
          <button class="btn" @click="addField('text')">+ Text Input</button>
          <button class="btn" @click="addField('textarea')">+ Text Area</button>
          <button class="btn" @click="addField('checkbox')">+ Checkbox</button>
          <button class="btn" @click="addField('radio')">+ Radio</button>
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
            <input
              class="input"
              v-model="f.label"
              :placeholder="f.type === 'text'
                ? 'Text Field'
                : f.type === 'textarea'
                ? 'Textarea Field'
                : f.type === 'checkbox'
                ? 'Checkbox Field'
                : 'Radio Field'"
            />

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
        <v-btn color="primary" @click="saveForm">Save Form</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <!-- Edit Modal Component -->
  <EditFormModal
    v-model="showEditModal"
    :formData="editFormData"
    @save="saveEdit"
  />

  <PreviewFormModal
  v-model="showPreviewModal"
  :formData="previewFormData"
/>
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
  flex-wrap: wrap; /* allow wrapping */
  gap: 12px; /* reduce gap for better responsiveness */
  justify-content: flex-start;
}

@media (min-width: 1024px) {
  .card-actions {
    justify-content: space-between; /* spread buttons on desktop */
    gap: 20px;
  }
}

.small-btn {
  min-height: 32px !important;
  font-size: 13px !important;
  padding: 4px 14px !important;
  flex: 1 1 auto; /* allow buttons to resize */
  max-width: 150px; /* prevent oversized buttons */
}

</style>
