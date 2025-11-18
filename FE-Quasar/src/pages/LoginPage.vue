<template>
  <q-page class="bg-primary flex flex-center login-page">
    <div
      class="login-card column items-center q-pa-lg q-pa-sm-md q-pa-xs-sm"
      style="border-radius: 20px; background: white"
    >
      <!-- Logo -->
      <img src="/logos/medisinLogo.png" alt="Medisin" class="logo q-mb-lg" />

      <!-- Form -->
      <form @submit.prevent="submitLogin" class="full-width">
        <div class="q-gutter-md">
          <!-- Email Input -->
          <q-input
            v-model="form.email"
            label="Email"
            filled
            dense
            type="text"
            class="full-width"
            input-class="bg-grey-2 input-rounded"
            :rules="[(val) => (val && val.length > 0) || 'Email harus diisi']"
            lazy-rules
          />

          <!-- Password Input -->
          <q-input
            v-model="form.password"
            label="Password"
            filled
            dense
            :type="showPassword ? 'text' : 'password'"
            class="full-width"
            input-class="bg-grey-2 input-rounded"
            :rules="[(val) => (val && val.length > 0) || 'Password harus diisi']"
            lazy-rules
          >
            <template v-slot:append>
              <q-icon
                :name="showPassword ? 'visibility' : 'visibility_off'"
                class="cursor-pointer text-grey-7"
                @click="showPassword = !showPassword"
              />
            </template>
          </q-input>

          <!-- Login Button -->
          <q-btn
            label="LOGIN"
            type="submit"
            color="primary"
            unelevated
            class="full-width text-white login-btn"
            :loading="loading"
          />
        </div>
      </form>

      <!-- Footer -->
      <p class="text-caption text-grey-6 q-mt-lg q-mb-none">
        © 2025 Medisin. All rights reserved.
      </p>
    </div>
  </q-page>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useQuasar } from 'quasar'
import { api } from 'boot/axios'
import { encryptAES } from 'src/utils/encryption'

const router = useRouter()
const $q = useQuasar()
const loading = ref(false)
const showPassword = ref(false)

const form = ref({
  email: '',
  password: '',
})

const submitLogin = async () => {
  if (!form.value.email || !form.value.password) {
    $q.notify({
      type: 'warning',
      message: 'Email dan Password harus diisi',
      position: 'top',
    })
    return
  }

  loading.value = true

  try {
    // 🔥 Encrypt data sebelum dikirim ke API
    const encryptedPayload = {
      email: encryptAES(form.value.email),
      password: encryptAES(form.value.password),
    }

    // 🔥 Kirim payload terenkripsi
    const res = await api.post('/login', encryptedPayload)

    const data = res.data || {}

    const token = data.token || data.access_token || (data.data && data.data.token)

    if (token) {
      localStorage.setItem('auth_token', token)
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`
    }

    $q.notify({
      type: 'positive',
      message: data.message || 'Login berhasil',
      position: 'top',
    })

    router.push('/patients')
  } catch (err) {
    if (err.response) {
      const status = err.response.status
      const payload = err.response.data || {}

      if (status === 422) {
        const errors = payload.errors || {}
        const msgs = []
        Object.values(errors).forEach((v) => {
          if (Array.isArray(v)) msgs.push(...v)
          else msgs.push(v)
        })
        $q.notify({
          type: 'negative',
          message: msgs.length ? msgs.join(' | ') : payload.message || 'Validasi gagal',
          position: 'top',
        })
      } else {
        $q.notify({
          type: 'negative',
          message: payload.message || 'Login gagal',
          position: 'top',
        })
      }
    } else {
      $q.notify({
        type: 'negative',
        message: 'Network error',
        position: 'top',
      })
    }
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-primary {
  background: linear-gradient(135deg, #0066d3 0%, #0052a3 100%);
}

.login-page {
  min-height: 100vh;
  padding: 20px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.12);
}

.logo {
  max-width: 140px;
  width: 100%;
  height: auto;
}

.input-rounded {
  border-radius: 15px !important;
}

.login-btn {
  border-radius: 20px;
  height: 42px;
  font-weight: 600;
  letter-spacing: 1px;
  font-size: 14px;
  transition: all 0.3s ease;
}

.login-btn:hover {
  box-shadow: 0 4px 12px rgba(0, 102, 211, 0.3);
  transform: translateY(-2px);
}

/* Tablet */
@media (max-width: 768px) {
  .login-card {
    max-width: 360px;
  }

  .logo {
    max-width: 120px;
  }

  .login-btn {
    height: 40px;
    font-size: 13px;
  }
}

/* Mobile */
@media (max-width: 600px) {
  .login-page {
    padding: 16px;
  }

  .login-card {
    max-width: 100%;
    max-width: 340px;
    padding: 24px 16px !important;
  }

  .logo {
    max-width: 100px;
    margin-bottom: 20px !important;
  }

  .login-btn {
    height: 38px;
    font-size: 12px;
  }
}

/* Small Mobile */
@media (max-width: 360px) {
  .login-card {
    max-width: 100%;
    padding: 20px 12px !important;
  }

  .logo {
    max-width: 80px;
  }

  :deep(.q-input__control) {
    font-size: 13px;
  }
}
</style>
