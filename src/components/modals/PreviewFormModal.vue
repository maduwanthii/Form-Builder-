<script setup lang="ts">
import { computed, reactive, watch } from 'vue';

type Field = {
  id: number | string;
  type: 'text' | 'textarea' | 'checkbox' | 'radio' | 'select';
  label: string;
  required?: boolean;
  options?: string[];
};

const props = defineProps<{
  /** v-model for showing/hiding the modal */
  modelValue: boolean;
  /** { title: string, fields: Field[] } */
  formData: { title: string; fields: Field[] };
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void;
}>();

const show = computed({
  get: () => props.modelValue,
  set: (v: boolean) => emit('update:modelValue', v),
});

/**
 * Basic local model just to let controls be “selectable” in the preview.
 * (No submit logic – this is just a visual preview.)
 */
const model = reactive<Record<string, any>>({});

watch(
  () => props.formData,
  (fd) => {
    // reset model when a new form comes in
    Object.keys(model).forEach((k) => delete model[k]);
    (fd?.fields || []).forEach((f: Field) => {
      if (f.type === 'checkbox') model[f.id] = [];
      else model[f.id] = '';
    });
  },
  { immediate: true, deep: true },
);

const titleText = computed(() => props.formData?.title || 'Form Preview');
</script>

<template>
  <v-dialog v-model="show" max-width="920" scrollable>
    <v-card class="preview-card">
      <v-card-text class="pa-0">
        <div class="preview-wrap">
          <!-- Header bar -->
          <div class="preview-topbar">
            <div class="left">
              <v-btn icon variant="text" class="mr-1" disabled>
                <v-icon>mdi-arrow-left</v-icon>
              </v-btn>
              <div class="crumbs">
                <span class="crumb">Form Preview</span>
                <span class="sep">/</span>
                <span class="crumb dim">{{ titleText }}</span>
              </div>
            </div>
            <div class="right d-flex" style="gap: 10px;">
  <v-btn size="small" variant="tonal" prepend-icon="mdi-content-copy">Copy URL</v-btn>
  <v-btn size="small" variant="tonal" prepend-icon="mdi-tray-arrow-down">Export</v-btn>
  <v-btn size="small" color="primary" prepend-icon="mdi-share-variant">Share</v-btn>
  <v-btn size="small" variant="text" icon @click="show = false">
    <v-icon>mdi-close</v-icon>
  </v-btn>
</div>

          </div>

          <!-- Canvas -->
          <div class="canvas">
            <div class="form-panel">
              <h2 class="title">{{ titleText }}</h2>
              <p class="subtitle">Registration form for upcoming events</p>

              <!-- Render fields -->
              <div class="fields">
                <template v-for="f in formData.fields" :key="f.id">
                  <!-- Text -->
                  <div v-if="f.type === 'text'" class="field">
                    <label class="label">
                      {{ f.label }}
                      <span v-if="f.required" class="req">*</span>
                    </label>
                    <v-text-field
                      density="comfortable"
                      variant="solo"
                      rounded="lg"
                      hide-details
                      v-model="model[f.id]"
                    />
                  </div>

                  <!-- Textarea -->
                  <div v-else-if="f.type === 'textarea'" class="field">
                    <label class="label">
                      {{ f.label }}
                      <span v-if="f.required" class="req">*</span>
                    </label>
                    <v-textarea
                      density="comfortable"
                      variant="solo"
                      rounded="lg"
                      auto-grow
                      rows="3"
                      hide-details
                      v-model="model[f.id]"
                    />
                  </div>

                  <!-- Select (optional type support) -->
                  <div v-else-if="f.type === 'select'" class="field">
                    <label class="label">
                      {{ f.label }}
                      <span v-if="f.required" class="req">*</span>
                    </label>
                    <v-select
                      :items="f.options || []"
                      density="comfortable"
                      variant="solo"
                      rounded="lg"
                      hide-details
                      v-model="model[f.id]"
                      placeholder="Select an option"
                    />
                  </div>

                  <!-- Radio Group -->
                  <div v-else-if="f.type === 'radio'" class="field">
                    <label class="label">
                      {{ f.label }}
                      <span v-if="f.required" class="req">*</span>
                    </label>
                    <v-radio-group v-model="model[f.id]" hide-details>
                      <div class="choice-stack">
                        <v-radio
                          v-for="(opt, i) in f.options || []"
                          :key="i"
                          :label="opt"
                          :value="opt"
                        />
                      </div>
                    </v-radio-group>
                  </div>

                  <!-- Checkbox Group -->
                  <div v-else-if="f.type === 'checkbox'" class="field">
                    <label class="label">
                      {{ f.label }}
                      <span v-if="f.required" class="req">*</span>
                    </label>
                    <div class="choice-stack">
                      <v-checkbox
                        v-for="(opt, i) in f.options || []"
                        :key="i"
                        :label="opt"
                        :value="opt"
                        hide-details
                        density="comfortable"
                        v-model="model[f.id]"
                      />
                    </div>
                  </div>
                </template>
              </div>

              <v-btn class="submit" size="large" color="primary" block rounded="lg" disabled>
                Submit Form
              </v-btn>
            </div>
          </div>
        </div>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>

<style scoped>
.preview-card {
  border-radius: 14px;
  overflow: hidden;
}
.preview-wrap {
  background: #f7f8fb;
  min-height: 70vh;
}

/* Top bar */
.preview-topbar {
  height: 56px;
  padding: 0 14px;
  background: #fff;
  border-bottom: 1px solid #eef0f4;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.preview-topbar .left {
  display: flex;
  align-items: center;
}
.crumbs {
  display: inline-flex;
  gap: 8px;
  align-items: center;
  font-size: 14px;
}
.crumb {
  font-weight: 600;
  color: #111827;
}
.crumb.dim {
  color: #6b7280;
  font-weight: 500;
}
.sep {
  color: #9ca3af;
}

/* Canvas area */
.canvas {
  padding: 28px 18px 36px;
  display: flex;
  justify-content: center;
}
.form-panel {
  width: 760px;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 6px 22px rgba(17, 24, 39, 0.06);
  padding: 26px;
}
.title {
  font-size: 24px;
  font-weight: 800;
  margin: 2px 0 6px;
  color: #111827;
}
.subtitle {
  color: #6b7280;
  margin-bottom: 18px;
}

/* Fields */
.fields .field + .field {
  margin-top: 14px;
}
.label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 6px;
}
.req {
  color: #ef4444;
  margin-left: 4px;
}
.choice-stack {
  display: grid;
  grid-template-columns: 1fr;
  gap: 4px;
}

/* Submit */
.submit {
  margin-top: 22px;
  height: 46px;
  font-weight: 700;
}
</style>
