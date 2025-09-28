<script setup lang="ts">
import { ref } from 'vue';
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';
import '@mdi/font/css/materialdesignicons.css'

const page = ref({ title: 'Forms' });
const breadcrumbs = ref([
  { title: 'Forms', disabled: true, href: '#' }
]);

// Example forms list
const forms = ref([
  { title: 'Contact Form', preview: 12 },
  { title: 'Event Registration', preview: 8 },
  { title: 'Product Feedback', preview: 20 }
]);
</script>

<template>
  <div class="d-flex align-center justify-space-between mb-4">
  
    <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs" />

    <v-btn color="primary" prepend-icon="mdi-plus">
      Create Form
    </v-btn>
  </div>

  <v-row>
    <v-col
      v-for="(form, i) in forms"
      :key="i"
      cols="12"
      md="4"
    >
      <UiParentCard :title="form.title">
      
        <div class="d-flex align-center justify-space-between mt-2">
          <v-btn variant="tonal" size="small" color="primary">Edit</v-btn>
          <v-btn variant="tonal" size="small" color="primary">Preview</v-btn>
          <v-btn variant="tonal" size="small" color="primary">{{ form.preview }}</v-btn>
          <v-btn variant="tonal" size="small" color="primary">Delete</v-btn>

          <v-btn icon size="small" color="error" variant="text">
            <v-icon size="18">mdi-delete</v-icon>
          </v-btn>
        </div>
      </UiParentCard>
    </v-col>
  </v-row>
</template>
