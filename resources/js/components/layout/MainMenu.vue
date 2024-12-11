
<template>
  <div class="main-menu">
    <div class="container">
      <div>Me Garden</div>
      <div>Me Menu</div>
    </div>
  </div>
</template>

<script>

export default {}
</script>
<style lang="scss" scoped>
.main-menu {
  border-bottom: 1px solid $borderColour;
  .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1em 0;
    margin: 0 12%;
  }
}
</style>
