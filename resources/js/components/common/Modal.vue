<template>
  <div class="modal">
    <div class="modal-content">
      <div class="header-contain">
        <slot name="header"></slot>
        <div>
          <Button styling="bare" @click="$emit('close')">X</Button>
        </div>
      </div>
      <div class="content-contain">
        <slot name="content"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Modal'
}
</script>

<style scoped lang="scss">
.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  .modal-content {
    background: $black;
    border: 1px solid $grey-600;
    padding: 20px;
    border-radius: 5px;
    .header-contain {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1em;
    }
  }
}

</style>