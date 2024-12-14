<template>
  <form>
    <div class="form-inputs-contain">
      <slot name="inputs"></slot>
    </div>
    <div class="form-buttons-contain">
      <Button v-if="canDelete && !confirmDelete" class="danger link outline left-btn" @click="confirmDelete = true">{{ deleteText || 'Delete' }}</Button>
      <div v-if="confirmDelete" class="left-btn confirm-delete">
        <div>{{ deleteWarning || 'Are you sure?' }}</div>
        <Button class="sm danger" @click="$emit('remove')">Yes</Button> &nbsp;
        <Button class="sm danger outline" @click="confirmDelete = false">No</Button>
      </div>
      <slot name="buttons"></slot>
    </div>
  </form>
</template>

<script>
export default {
  name: 'Form',
  props: {
    canDelete: {
      type: Boolean,
      default: false
    },
    deleteText: {
      type: String,
      default: 'Delete'
    },
    deleteWarning: {
      type: String,
      default: 'Are you sure?'
    }
  },
  emits: ['remove'],
  data () {
    return {
      confirmDelete: false
    }
  },
  mounted () {
    this.$nextTick(() => {
      // focus on first input
      this.$el.querySelector('input')?.focus()
    })
  }
}
</script>

<style lang="scss" scoped>
form {
  :deep(.error) {
    color: #ff0000;
  }
  .form-inputs-contain {
    padding-bottom: 1em;
    > * {
      margin-bottom: 1em;
    }
  }
  .form-buttons-contain {
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
    .confirm-delete {
      display: flex;
      align-items: center;
      div {
        margin-right: 10px;
      }
    }
  }
}
</style>