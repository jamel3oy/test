<template>
  <div class="loading-page">
    <div class="spinner"></div>
    <p>{{loadingtext?loadingtext:'Loading...'}}</p>
  </div>
</template>

<script>
export default {
    props: ['loadingtext']
}
</script>

<style>
.loading-page {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

.spinner {
  width: 4rem;
  height: 4rem;
  border: 0.4rem solid #ccc;
  border-top-color: #333;
  border-radius: 50%;
  animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

p {
  margin-top: 1rem;
}
</style>