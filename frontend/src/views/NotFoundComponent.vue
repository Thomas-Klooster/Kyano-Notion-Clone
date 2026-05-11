
<template>
  <div class="not-found-container">
    <div class="NotFoundBox">
     <h4>404 fout</h4>
      <h1>Deze pagina kunnen we niet vinden</h1>
      <p>Zoek in het menu naar een andere pagina.</p>
     </div>
  </div>
</template>

<script>
  export default {
    name: 'NotFoundComponent'
  }
</script>

<style scoped>

* {
     text-decoration: none;
     color: var(--color-white);
     font-family: var(--font-family-base);
}

.not-found-container {
  min-height: 80vh;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-wrap: wrap;
  text-align: center;
  padding: 0 16px;
}

.NotFoundBox {
  padding: 90px;
  background-color: var(--color-bg-error-soft);
  width: 1500px;
  border-radius: var(--radius-3xl);
}

h1 {
   font-size: 2.8rem;
   color: var(--color-white);
}

p {
     padding-top: 1rem;
     font-size: 16px;
}

</style>