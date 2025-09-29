<script setup lang="ts">
import { reactive } from 'vue'

type FieldType = 'text' | 'textarea' | 'checkbox' | 'radio'
type Field = {
  id: string
  type: FieldType
  label: string
  required: boolean
  options: string[]
}

const newForm = reactive<{ title: string; fields: Field[] }>({
  title: '',
  fields: []
})

function uid() {
  return `f_${Math.random().toString(36).slice(2, 10)}_${Date.now().toString(36)}`
}

function placeholderFor(type: FieldType) {
  if (type === 'text') return 'Text Field'
  if (type === 'textarea') return 'Textarea Field'
  if (type === 'checkbox') return 'Checkbox Field'
  return 'Radio Field'
}

function safeNormalizeField(f: Partial<Field>): Field {
  const type = (f.type ?? 'text') as FieldType
  return {
    id: f.id ?? uid(),
    type,
    label: f.label ?? '',
    required: Boolean(f.required),
    options: type === 'checkbox' || type === 'radio'
      ? (Array.isArray(f.options) && f.options.length ? [...f.options] : ['Option 1'])
      : []
  }
}

function addField(type: FieldType) {
  newForm.fields.push(
    safeNormalizeField({
      id: uid(),
      type,
      label: '',
      required: false,
      options: type === 'checkbox' || type === 'radio' ? ['Option 1'] : []
    })
  )
}

function removeField(index: number) {
  newForm.fields.splice(index, 1)
}

function move(index: number, delta: number) {
  const target = index + delta
  if (target < 0 || target >= newForm.fields.length) return
  const item = newForm.fields[index]
  newForm.fields.splice(index, 1)
  newForm.fields.splice(target, 0, item)
}

function addOption(field: Field) {
  if (!Array.isArray(field.options)) field.options = []
  field.options.push(`Option ${field.options.length + 1}`)
}

function removeOption(field: Field, oi: number) {
  field.options.splice(oi, 1)
  if (field.options.length === 0) field.options.push('Option 1')
}

function resetForm() {
  newForm.title = ''
  newForm.fields = []
}

function saveForm() {
  const payload = {
    title: (newForm.title || '').trim(),
    fields: newForm.fields.map((f) => ({
      id: f.id,
      type: f.type,
      label: (f.label || '').trim(),
      required: !!f.required,
      options:
        f.type === 'checkbox' || f.type === 'radio'
          ? f.options.map((o) => String(o || '').trim()).filter(Boolean)
          : []
    }))
  }

  // Replace with your API call
  console.log('Saved form:', payload)

  resetForm()
}
</script>

<template>
  <v-container>
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

        <div v-if="newForm.fields.length === 0" class="empty">
          No fields yet. Add one above.
        </div>

        <div class="list">
          <div
            v-for="(f, i) in newForm.fields"
            :key="f.id"
            class="field-item"
          >
            <div class="row" style="justify-content: space-between; margin-bottom: 8px;">
              <div class="row" style="gap: 10px">
                <span class="badge">{{ f.type }}</span>
                <label class="row small" style="gap: 6px;">
                  <input type="checkbox" v-model="f.required" /> required
                </label>
              </div>

              <div class="actions">
                <button class="btn" @click="move(i, -1)" :disabled="i === 0">↑</button>
                <button class="btn" @click="move(i, 1)" :disabled="i === newForm.fields.length - 1">↓</button>
                <button class="btn danger" @click="removeField(i)">Delete</button>
              </div>
            </div>

            <label class="label">Label</label>
            <input
              class="input"
              v-model="f.label"
              :placeholder="placeholderFor(f.type)"
            />

            <template v-if="f.type === 'checkbox' || f.type === 'radio'">
              <div class="hr"></div>
              <label class="label">Options</label>

              <div
                v-for="(op, oi) in f.options"
                :key="`${f.id}-op-${oi}`"
                class="row"
                style="gap: 10px;"
              >
                <input class="input" v-model="f.options[oi]" />
                <button class="btn" @click="removeOption(f, oi)">🗑</button>
              </div>

              <div style="margin-top: 8px;">
                <a class="link" href="#" @click.prevent="addOption(f)">+ Add option</a>
              </div>
            </template>
          </div>
        </div>
      </v-card-text>

      <v-card-actions class="justify-end card-actions">
        <v-btn color="primary" @click="saveForm">Save Form</v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
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
.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.btn.danger {
  color: #b91c1c;
  border-color: #fecaca;
  background: #fff5f5;
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
  text-transform: capitalize;
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
.hr {
  height: 1px;
  width: 100%;
  background: #eee;
  margin: 10px 0;
}
.field-item {
  border: 1px solid #eee;
  border-radius: 6px;
  padding: 12px;
  margin-bottom: 12px;
  background: #fafafa;
}
.toolbar .small {
  font-size: 12px;
  margin-right: 8px;
  color: #666;
}
</style>
