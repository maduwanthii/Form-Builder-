<script setup lang="ts">
import { defineProps, defineEmits, computed } from 'vue';

/**
 * Props:
 * - modelValue: controls open/close (used by parent v-model)
 * - newForm: the reactive form object from parent (title, fields[])
 * - handlers: we reuse the parent's functions so inner markup stays unchanged
 */
const props = defineProps<{
  modelValue: boolean;
  newForm: { title: string; fields: any[] };
  addField: (type: string) => void;
  removeField: (i: number) => void;
  move: (i: number, direction: number) => void;
  addOption: (field: any) => void;
  removeOption: (field: any, index: number) => void;
  cancel: () => void;
  saveForm: () => void;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
}>();

// Proxy the dialog's v-model to the parent
const model = computed({
  get: () => props.modelValue,
  set: (v: boolean) => emit('update:modelValue', v),
});

// Expose props for template (keeps your markup unchanged aside from v-model target)
const newForm = props.newForm;
const addField = props.addField;
const removeField = props.removeField;
const move = props.move;
const addOption = props.addOption;
const removeOption = props.removeOption;
const cancel = props.cancel;
const saveForm = props.saveForm;
</script>

<template>
  <!-- Only necessary change: bind to `model` (the component's v-model proxy) -->
  <v-dialog v-model="model" width="600">
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
</template>
