<script setup lang="ts">
import { ref } from 'vue';

const exportCSV = () => {
  const headers = ['ID', 'Full Name', 'Email', 'Message', 'IP Address', 'Submitted'];
  
  const rows = submissions.value.map(sub => [
    sub.id,
    `"${sub.fullName}"`,
    `"${sub.email}"`,
    `"${sub.message}"`,
    sub.ip,
    sub.submitted,
  ]);

  const csvContent =
    [headers.join(','), ...rows.map(r => r.join(','))].join('\n');

  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement('a');
  link.href = URL.createObjectURL(blob);
  link.setAttribute('download', 'submissions.csv');
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const submissions = ref([
  {
    id: 1,
    fullName: 'Maduwanthi Lakshika',
    email: 'madu@email.com',
    message: 'I would like to know more about your services.',
    ip: '192.168.1.100',
    submitted: '2024-09-30 20:00:00',
  },
  {
    id: 2,
    fullName: 'Lakshika Rathnayaka',
    email: 'lakshiik@email.com',
    message: 'Please send me your price list.',
    ip: '192.168.1.120',
    submitted: '2024-09-29 21:30:00',
  },
]);

const selected = ref(submissions.value[0]);
const search = ref('');

const selectSubmission = (s: any) => {
  selected.value = s;
};
</script>

<template>
  <div class="submissions-wrap">
    <!-- Header -->
    <div class="header">
      <h2>Form Submissions</h2>
     <v-btn color="success" prepend-icon="mdi-tray-arrow-down" @click="exportCSV">Export CSV</v-btn>

    </div>

    <div class="content">
      <!-- Left panel -->
      <div class="left">
        <div class="search-bar">
          <v-text-field
            v-model="search"
            placeholder="Search submissions..."
            density="comfortable"
            variant="solo"
            hide-details
            clearable
            rounded="lg"
          />
        </div>

        <div class="submission-list">
          <div
            v-for="s in submissions"
            :key="s.id"
            class="submission-item"
            :class="{ active: selected?.id === s.id }"
            @click="selectSubmission(s)"
          >
            <p class="title">Submission #{{ s.id }}</p>
            <p class="small">Full Name: {{ s.fullName }}</p>
            <p class="small">Email Address: {{ s.email }}</p>
            <p class="more">+1 more fields</p>
            <span class="date">{{ new Date(s.submitted).toLocaleDateString() }}</span>
          </div>
        </div>
      </div>

      <!-- Right panel -->
      <div class="right" v-if="selected">
        <h3 class="details-title">Submission Details</h3>
        <div class="meta">
          <span>Submitted: {{ new Date(selected.submitted).toLocaleString() }}</span>
          <span>IP Address: {{ selected.ip }}</span>
        </div>

        <div class="form-data">
          <div class="field">
            <label>Full Name</label>
            <div class="value">{{ selected.fullName }}</div>
          </div>
          <div class="field">
            <label>Email Address</label>
            <div class="value">{{ selected.email }}</div>
          </div>
          <div class="field">
            <label>Message</label>
            <div class="value">{{ selected.message }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.submissions-wrap {
  padding: 20px;
  background: #f9fafb;
  height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.header h2 {
  font-size: 20px;
  font-weight: 700;
  color: #111827;
}

/* Content split */
.content {
  display: flex;
  flex: 1;
  gap: 20px;
}

/* Left panel */
.left {
  flex: 1; /* equal size */
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
  display: flex;
  flex-direction: column;
}
.search-bar {
  padding: 10px;
  border-bottom: 1px solid #f1f3f5;
}
.submission-list {
  overflow-y: auto;
}
.submission-item {
  padding: 12px 16px;
  border-bottom: 1px solid #f1f3f5;
  cursor: pointer;
  position: relative;
}
.submission-item.active {
  background: #eef2ff;
}
.submission-item .title {
  font-weight: 600;
  margin-bottom: 4px;
  color: #111827;
}
.submission-item .small {
  font-size: 13px;
  color: #374151;
  margin-bottom: 2px;
}
.submission-item .more {
  font-size: 12px;
  color: #6b7280;
}
.submission-item .date {
  font-size: 12px;
  color: #9ca3af;
  position: absolute;
  top: 12px;
  right: 12px;
}

/* Right panel */
.right {
  flex: 1; /* equal size */
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
}
.details-title {
  font-size: 18px;
  font-weight: 700;
  margin-bottom: 16px;
}
.meta {
  font-size: 13px;
  color: #6b7280;
  display: flex;
  justify-content: space-between;
  margin-bottom: 20px;
}
.form-data .field {
  margin-bottom: 16px;
}
.form-data .field label {
  font-size: 13px;
  font-weight: 600;
  color: #111827;
  margin-bottom: 4px;
  display: block;
}
.form-data .field .value {
  background: #f9fafb;
  padding: 10px;
  border-radius: 6px;
  font-size: 14px;
  color: #111827;
}

/* Responsive */
@media (max-width: 768px) {
  .content {
    flex-direction: column;
  }
  .left,
  .right {
    flex: unset;
    width: 100%;
  }
}
</style>
