<template>
  <!-- Header Logout -->
  <div class="row justify-end q-mb-md">
    <q-btn
      color="negative"
      icon="logout"
      label="Logout"
      flat
      class="logout-btn"
      @click="handleLogout"
    />
  </div>

  <q-page class="bg-white q-pa-md">
    <div class="q-mx-auto" style="max-width: 1200px">
      <!-- Logo -->
      <div class="text-center q-mb-lg">
        <img
          src="/logos/medisinLogo.png"
          alt="Medisin Logo"
          style="height: 60px"
          class="responsive-logo"
        />
      </div>

      <!-- Buttons -->
      <div class="row q-mb-md justify-start q-gutter-sm">
        <q-btn
          color="primary"
          label="TAMBAH PASIEN"
          class="responsive-btn"
          @click="openAddDialog"
        />

        <q-btn
          color="green"
          label="BUAT KUNJUNGAN"
          class="responsive-btn"
          @click="goToCreateVisit"
        />
      </div>

      <!-- Table -->
      <q-card flat bordered class="shadow-1">
        <q-card-section>
          <div v-if="loading" class="text-center q-pa-md">
            <q-spinner color="primary" size="50px" />
          </div>

          <div v-else-if="patients.length === 0" class="text-center text-grey q-pa-md">
            <p class="text-subtitle2">Data pasien belum tersedia.</p>
          </div>

          <div v-else class="table-responsive">
            <q-table
              :rows="patients"
              :columns="columns"
              row-key="id"
              flat
              bordered
              v-model:pagination="pagination"
              class="responsive-table"
            >
              <template v-slot:body-cell-index="props">
                <q-td>{{ props.rowIndex + 1 }}</q-td>
              </template>
            </q-table>
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- MODAL BUAT KUNJUNGAN -->
    <q-dialog v-model="dialogVisit" persistent @hide="resetVisitForm">
      <q-card style="width: 100%; max-width: 600px; border-radius: 20px">
        <!-- Header -->
        <q-card-section class="bg-primary text-white q-pa-md" style="border-radius: 20px 20px 0 0">
          <div class="row justify-between items-center">
            <div class="text-h6 text-weight-bold">BUAT KUNJUNGAN</div>
            <q-btn icon="close" flat round dense color="white" @click="dialogVisit = false" />
          </div>
        </q-card-section>

        <!-- Body -->
        <q-card-section class="q-pa-lg">
          <!-- Cari Pasien Section -->
          <div class="q-mb-lg">
            <div class="text-subtitle2 text-weight-medium q-mb-sm">Cari Pasien</div>
            <div class="row q-col-gutter-sm">
              <div class="col-8">
                <q-input
                  filled
                  dense
                  placeholder="No Rekam Medik"
                  v-model="visitForm.noRekamMedik"
                  outlined
                  class="full-width"
                />
              </div>
              <div class="col-4">
                <q-btn
                  color="primary"
                  label="CARI"
                  class="full-width"
                  style="height: 40px"
                  @click="searchPatient"
                />
              </div>
            </div>
          </div>

          <!-- Data Pasien Section -->
          <div>
            <div class="text-subtitle2 text-weight-medium q-mb-md">Data Pasien</div>
            <div class="row q-col-gutter-md">
              <div class="col-xs-12 col-sm-6">
                <q-input
                  filled
                  dense
                  placeholder="Nama Pasien"
                  v-model="visitForm.namaPasien"
                  outlined
                  readonly
                  class="full-width bg-grey-2"
                />
              </div>
              <div class="col-xs-12 col-sm-6">
                <q-input
                  filled
                  dense
                  placeholder="NIK"
                  v-model="visitForm.nik"
                  outlined
                  readonly
                  class="full-width bg-grey-2"
                />
              </div>
            </div>

            <q-input
              filled
              dense
              type="textarea"
              placeholder="Alamat"
              v-model="visitForm.alamat"
              outlined
              readonly
              rows="3"
              class="full-width bg-grey-2 q-mt-md"
            />
          </div>
        </q-card-section>

        <!-- Footer -->
        <q-card-section class="text-center q-pa-lg">
          <q-btn
            label="BUAT KUNJUNGAN"
            color="primary"
            class="full-width"
            style="border-radius: 10px; height: 45px; font-weight: 600"
            :loading="creatingVisit"
            @click="createVisit"
          />
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- MODAL TAMBAH PASIEN -->
    <q-dialog v-model="dialogAdd" persistent @hide="resetForm">
      <q-card style="width: 100%; max-width: 600px; border-radius: 20px">
        <q-card-section class="bg-primary text-white" style="border-radius: 20px 20px 0 0">
          <div class="row justify-between items-center">
            <div class="text-h6">TAMBAH PASIEN</div>
            <q-btn icon="close" flat round dense color="white" @click="dialogAdd = false" />
          </div>
        </q-card-section>

        <!-- Body -->
        <q-card-section class="q-gutter-md q-pa-lg">
          <q-input
            filled
            dense
            label="No. Rekam Medik"
            v-model="form.no_rekam_medik"
            outlined
            readonly
            class="full-width bg-grey-3"
            :error="!!errors.no_rekam_medik"
            :error-message="errors.no_rekam_medik"
          />

          <div class="row q-col-gutter-md">
            <div class="col-xs-12 col-sm-6">
              <q-input
                filled
                dense
                label="Nama Pasien *"
                v-model="form.nama"
                outlined
                class="full-width"
                :error="!!errors.nama"
                :error-message="errors.nama"
                @input="errors.nama = ''"
              />
            </div>

            <div class="col-xs-12 col-sm-6">
              <q-input
                filled
                dense
                label="NIK *"
                v-model="form.nik"
                outlined
                class="full-width"
                maxlength="16"
                :error="!!errors.nik"
                :error-message="errors.nik"
                @input="errors.nik = ''"
              />
            </div>
          </div>

          <q-input
            filled
            dense
            type="textarea"
            label="Alamat *"
            v-model="form.alamat"
            autogrow
            outlined
            class="full-width"
            :error="!!errors.alamat"
            :error-message="errors.alamat"
            @input="errors.alamat = ''"
          />
        </q-card-section>

        <!-- Footer -->
        <q-card-section class="text-center q-pa-lg bg-grey-1">
          <div class="row q-gutter-md justify-center">
            <q-btn
              label="BATAL"
              color="grey"
              class="col-xs-12 col-sm-auto"
              style="border-radius: 10px; min-width: 120px"
              @click="dialogAdd = false"
            />
            <q-btn
              label="SIMPAN"
              color="primary"
              class="col-xs-12 col-sm-auto"
              style="border-radius: 10px; min-width: 120px"
              @click="savePatient"
            />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'boot/axios'

const router = useRouter()
const $q = useQuasar()
const loading = ref(true)
const patients = ref([])
const dialogAdd = ref(false)
const dialogVisit = ref(false)
const creatingVisit = ref(false)

const pagination = ref({
  rowsPerPage: 10,
})

const columns = [
  { name: 'index', label: 'NO', field: 'index', align: 'left' },
  { name: 'no_rekam_medik', label: 'NO REKAM MEDIK', field: 'no_rekam_medik', align: 'left' },
  { name: 'nama', label: 'NAMA PASIEN', field: 'nama', align: 'left' },
  { name: 'nik', label: 'NIK', field: 'nik', align: 'left' },
  { name: 'alamat', label: 'ALAMAT', field: 'alamat', align: 'left' },
  { name: 'jml_kunjungan', label: 'JML KUNJUNGAN', field: 'jml_kunjungan', align: 'center' },
]

const form = ref({
  no_rekam_medik: '',
  nama: '',
  nik: '',
  alamat: '',
})

const visitForm = ref({
  noRekamMedik: '',
  namaPasien: '',
  nik: '',
  alamat: '',
})

const errors = ref({
  no_rekam_medik: '',
  nama: '',
  nik: '',
  alamat: '',
})

const generateRM = async () => {
  try {
    const response = await api.get('/patients/generate-rm')
    form.value.no_rekam_medik = response.data.data
  } catch (err) {
    console.error(err)
    $q.notify({
      type: 'negative',
      message: 'Gagal mengambil nomor rekam medik',
    })
  }
}

const openAddDialog = async () => {
  await generateRM()
  dialogAdd.value = true
}

const resetForm = () => {
  form.value = { no_rekam_medik: '', nama: '', nik: '', alamat: '' }
  errors.value = { no_rekam_medik: '', nama: '', nik: '', alamat: '' }
}

const resetVisitForm = () => {
  visitForm.value = {
    noRekamMedik: '',
    namaPasien: '',
    nik: '',
    alamat: '',
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const response = await api.get('/patients')
    // API return: { data: { data: [...], current_page, last_page, ... }, message, success }
    // Ekstrak array dari response.data.data
    const data = response.data?.data

    if (Array.isArray(data?.data)) {
      patients.value = data.data
    } else if (Array.isArray(data)) {
      patients.value = data
    } else {
      patients.value = []
    }
  } catch (err) {
    console.error('loadData error:', err)
    $q.notify({
      type: 'negative',
      message: 'Gagal memuat data pasien',
      position: 'top',
    })
    patients.value = []
  } finally {
    loading.value = false
  }
}

const savePatient = async () => {
  errors.value = { no_rekam_medik: '', nama: '', nik: '', alamat: '' }
  if (
    !form.value.no_rekam_medik?.trim() ||
    !form.value.nama?.trim() ||
    !form.value.nik?.trim() ||
    !form.value.alamat?.trim()
  ) {
    if (!form.value.no_rekam_medik?.trim())
      errors.value.no_rekam_medik = 'No. Rekam Medik harus diisi'
    if (!form.value.nama?.trim()) errors.value.nama = 'Nama pasien harus diisi'
    if (!form.value.nik?.trim()) errors.value.nik = 'NIK harus diisi'
    if (!form.value.alamat?.trim()) errors.value.alamat = 'Alamat harus diisi'

    $q.notify({
      type: 'warning',
      message: 'Periksa field yang wajib diisi',
    })
    return
  }

  try {
    const res = await api.post('/patients', {
      no_rekam_medik: form.value.no_rekam_medik.trim(),
      nama: form.value.nama.trim(),
      nik: form.value.nik.trim(),
      alamat: form.value.alamat.trim(),
    })

    dialogAdd.value = false
    resetForm()
    await loadData()

    $q.notify({
      type: 'positive',
      message: res.data?.message || 'Data pasien berhasil disimpan',
      position: 'top',
    })
  } catch (err) {
    console.error('savePatient error', err)

    // handle 422 validation errors from backend
    if (err.response?.status === 422) {
      const validation = err.response.data?.errors || {}
      // map arrays to readable strings per field
      errors.value = {
        no_rekam_medik: Array.isArray(validation.no_rekam_medik)
          ? validation.no_rekam_medik.join(' ')
          : validation.no_rekam_medik || '',
        nama: Array.isArray(validation.nama) ? validation.nama.join(' ') : validation.nama || '',
        nik: Array.isArray(validation.nik) ? validation.nik.join(' ') : validation.nik || '',
        alamat: Array.isArray(validation.alamat)
          ? validation.alamat.join(' ')
          : validation.alamat || '',
      }

      // show first error summary
      const firstMsg = Object.values(errors.value).find((v) => v)
      $q.notify({
        type: 'negative',
        message: firstMsg || err.response.data?.message || 'Validasi gagal',
        position: 'top',
      })
    } else {
      const msg = err.response?.data?.message || 'Gagal menyimpan data'
      $q.notify({
        type: 'negative',
        message: msg,
        position: 'top',
      })
    }
  }
}

const searchPatient = () => {
  const key = visitForm.value.noRekamMedik?.trim()
  if (!key) {
    $q.notify({ type: 'warning', message: 'Masukkan No Rekam Medik', position: 'top' })
    return
  }

  const patient = patients.value.find((p) => p.no_rekam_medik === key)
  if (patient) {
    visitForm.value.namaPasien = patient.nama
    visitForm.value.nik = patient.nik
    visitForm.value.alamat = patient.alamat
    $q.notify({
      type: 'positive',
      message: 'Data pasien ditemukan',
      position: 'top',
    })
  } else {
    resetVisitForm()
    visitForm.value.noRekamMedik = key
    $q.notify({
      type: 'warning',
      message: 'Pasien tidak ditemukan',
      position: 'top',
    })
  }
}

const createVisit = async () => {
  if (!visitForm.value.noRekamMedik?.trim() || !visitForm.value.namaPasien?.trim()) {
    $q.notify({
      type: 'warning',
      message: 'Silakan cari pasien terlebih dahulu',
      position: 'top',
    })
    return
  }

  creatingVisit.value = true
  try {
    const payload = {
      no_rekam_medik: visitForm.value.noRekamMedik.trim(),
      nama: visitForm.value.namaPasien.trim(),
      nik: visitForm.value.nik?.trim(),
      alamat: visitForm.value.alamat?.trim(),
    }

    const res = await api.post('/visits', payload)

    // tutup dialog dan reset form
    dialogVisit.value = false
    resetVisitForm()

    // Update local patients array agar tabel langsung ter-reflect
    const idx = patients.value.findIndex((p) => p.no_rekam_medik === payload.no_rekam_medik)
    if (idx !== -1) {
      // jika BE mengembalikan jumlah kunjungan terbaru, pakai itu
      const updatedCount = res.data?.data?.jml_kunjungan ?? res.data?.jml_kunjungan ?? null
      if (updatedCount !== null) {
        patients.value[idx].jml_kunjungan = updatedCount
      } else {
        patients.value[idx].jml_kunjungan = (patients.value[idx].jml_kunjungan || 0) + 1
      }
      // pastikan Vue mendeteksi perubahan objek
      patients.value.splice(idx, 1, { ...patients.value[idx] })
    } else {
      // jika pasien baru dibuat di BE bersama kunjungan, tambahkan ke daftar
      const newPatient = res.data?.data ?? null
      if (newPatient && typeof newPatient === 'object') {
        patients.value.unshift(newPatient)
      }
    }

    $q.notify({
      type: 'positive',
      message: 'Kunjungan berhasil dibuat',
      position: 'top',
    })
  } catch (err) {
    console.error('createVisit error:', err)
    const msg = err.response?.data?.message || 'Gagal membuat kunjungan'
    $q.notify({
      type: 'negative',
      message: msg,
      position: 'top',
    })
  } finally {
    creatingVisit.value = false
  }
}

const handleLogout = async () => {
  $q.dialog({
    title: 'Logout',
    message: 'Apakah Anda yakin ingin logout?',
    cancel: true,
    persistent: true,
  }).onOk(async () => {
    try {
      await api.post('/auth/logout')
      localStorage.removeItem('auth_token')
      router.push('/login')

      $q.notify({
        type: 'positive',
        message: 'Berhasil logout',
        position: 'top',
      })
    } catch (err) {
      console.error(err)
      localStorage.removeItem('auth_token')
      router.push('/login')
    }
  })
}

const goToCreateVisit = () => {
  dialogVisit.value = true
}

onMounted(() => {
  loadData()
})
</script>

// ...existing code...

<style scoped>
.responsive-logo {
  max-width: 100%;
  height: auto;
}

.responsive-btn {
  width: 100%;
  max-width: 200px;
}

.table-responsive {
  overflow-x: auto;
}

.responsive-table {
  min-width: 600px;
}

@media (max-width: 600px) {
  .responsive-btn {
    max-width: 100%;
    font-size: 12px;
    padding: 8px 12px;
  }

  .responsive-table {
    font-size: 12px;
  }

  :deep(.q-table__card) {
    box-shadow: none;
  }
}

.logout-btn {
  border-radius: 10px;
  padding: 6px 16px;
}

@media (max-width: 600px) {
  .logout-btn {
    width: 100%;
    margin-bottom: 10px;
  }
}
</style>
