<template>
  <div class="modal" @click.self="$emit('close')">
    <div class="modal-content">
      <div class="header-contain">
        <slot name="header"></slot>
        <div class="cancel">
          <Button classes="link outline primary" @click="$emit('close')">{{ cancelText }}</Button>
        </div>
      </div>
      <div class="content-contain">
        <slot name="content"></slot>
      </div>
      <div class="buttons-contain">
        <slot name="buttons"></slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Modal',
  emits: ['close'],
  props: {
    cancelText: {
      type: String,
      default: 'Cancel'
    }
  },
  mounted () {
    document.addEventListener('keydown', this.closeOnEscape)
  },
  beforeUnmount () {
    document.removeEventListener('keydown', this.closeOnEscape)
  },
  methods: {
    closeOnEscape (e) {
      if (e.key === 'Escape') {
        this.$emit('close')
      }
    }
  }
}
</script>

<style scoped lang="scss">
.modal {
  z-index: 999;
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
    overflow: auto;
    max-height: 80vh;
    width: 640px;
    border-radius: 0.5em;
    background: $backgroundColour;
    padding: 2em;
    .header-contain {
      position: relative;
      margin-bottom: 1em;
      :deep(p) {
        margin-top: 0.5em;
        margin-bottom: 2em;
      }
      .cancel {
        position: absolute;
        right: 0;
        top: 0;
      }
    }
  }
  .buttons-contain {
    display: flex;
    justify-content: flex-end;
    margin-top: 1em;
    > * {
      margin-left: 1em;
    }
    .left-btn {
      margin-left: 0;
      margin-right: auto;
    }
  }
}

</style>