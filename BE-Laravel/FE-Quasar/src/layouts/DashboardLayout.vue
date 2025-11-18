<template>
  <q-layout view="lHh Lpr lFf" class="bg-primary">
    <q-page-container>
      <div class="page-wrapper flex flex-center">
        <div class="content-box">
          <router-view />
        </div>
      </div>
    </q-page-container>
  </q-layout>
</template>

<style>
.bg-primary {
  background: #0066d3;
}

.page-wrapper {
  min-height: 100vh;
  padding: 25px;
}

.content-box {
  width: 100%;
  min-height: 90vh;
  background: white;
  border-radius: 20px;
  padding: 40px;
  max-width: 1400px;
  margin: auto;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
