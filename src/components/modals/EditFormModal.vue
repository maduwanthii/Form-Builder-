<script setup lang="ts">
import { ref, watch, defineEmits, defineProps, computed } from 'vue';

const props = defineProps<{
  modelValue: boolean;
  formData: { title: string; description: string; fields: any[] };
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
  (e: 'save', updatedForm: { title: string; description: string; fields: any[] }): void;
}>();

const isOpen = computed({
  get: () => props.modelValue,
  set: (v: boolean) => emit('update:modelValue', v),
});

// ✅ Declare before using it
const localForm = ref<{ title: string; description: string; fields: any[] }>({
  title: '',
  description: '',
  fields: [],
});

watch(
  () => props.formData,
  (val) => {
    const safe = val || { title: '', description: '', fields: [] };
    localForm.value = JSON.parse(JSON.stringify(safe));
  },
  { immediate: true }
);

const genId = () => Date.now() + Math.random();

const addField = (type: string) => {
  const field = {
    id: genId(),
    type,
    label: '',
    required: false,
    options: type === 'checkbox' || type === 'radio' ? [''] : []
  };
  localForm.value.fields.push(field);
};

const removeField = (i: number) => localForm.value.fields.splice(i, 1);

const move = (i: number, direction: number) => {
  const newIndex = i + direction;
  if (newIndex < 0 || newIndex >= localForm.value.fields.length) return;
  const temp = localForm.value.fields[i];
  localForm.value.fields[i] = localForm.value.fields[newIndex];
  localForm.value.fields[newIndex] = temp;
};

const addOption = (field: any) => field.options.push('');
const removeOption = (field: any, index: number) => field.options.splice(index, 1);

const close = () => { isOpen.value = false; };

const save = () => {
  emit('save', JSON.parse(JSON.stringify(localForm.value)));
  close();
};
</script>

<template>
  <v-dialog v-model="isOpen" width="700">
    <v-card>
      <v-card-title>Edit Form</v-card-title>
      <v-card-text>
        <label class="label">Form Title</label>
        <input class="input" v-model="localForm.title" placeholder="Enter form title" />
       <label class="label">Form Description</label>
<input class="input" v-model="localForm.description" placeholder="Enter form description" />
        <div class="toolbar" style="margin: 10px 0">
          <span class="small">Add Fields:</span>
          <button class="btn" @click="addField('text')">+ Text Input</button>
          <button class="btn" @click="addField('textarea')">+ Text Area</button>
          <button class="btn" @click="addField('checkbox')">+ Checkbox</button>
          <button class="btn" @click="addField('radio')">+ Radio</button>
        </div>

        <div v-if="localForm.fields.length === 0" class="empty">No fields yet. Add one above.</div>

        <div class="list">
          <div v-for="(f, i) in localForm.fields" :key="f.id" class="field-item">
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
        <v-btn text @click="close">Cancel</v-btn>
        <v-btn color="primary" @click="save">Save Changes</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.label { font-weight: bold; display: block; margin-bottom: 4px; }
.input { width: 100%; padding: 6px 10px; margin-bottom: 12px; border: 1px solid #ddd; border-radius: 4px; }
.btn { padding: 4px 10px; margin: 2px; border: 1px solid #ccc; background: #f3f4f6; border-radius: 4px; font-size: 13px; cursor: pointer; }
.btn.danger { color: #b91c1c; border-color: #fecaca; }
.row { display: flex; align-items: center; }
.badge { background-color: #e2e8f0; padding: 2px 6px; border-radius: 4px; font-size: 12px; }
.link { color: #3b82f6; cursor: pointer; font-size: 13px; }
.empty { color: #999; font-style: italic; margin-bottom: 12px; }
</style>
