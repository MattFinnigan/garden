<template>
  <div :class="['list-item', {highlight}]">
    <div class="list-item-wrapper">
      <div class="img-contain">
        <slot name="image"></slot>
      </div>
      <div class="title-contain">
        <strong><slot name="title"></slot></strong>
        <em><slot name="description"></slot></em>
      </div>
    </div>
    <div class="actions">
      <slot name="actions"></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ListItem',
  props: {
    highlight: {
      type: Boolean,
      default: false
    }
  }
}
</script>

<style scoped lang="scss">
.list-item {
  padding: 10px;
  background: $grey-200;
  border-bottom: 1px solid #ccc;
  cursor: pointer;
  .list-item-wrapper {
    display: flex;
    .img-contain {
      margin-right: 5px;
      :deep(img) {
        height: 50px;
      }
    }
    .title-contain, .title-contain > * {
      font-size: $fsSmall;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }
  }
  .actions {
    display: flex;
    justify-content: flex-end;
    :deep(button) {
      margin-left: 5px;
    }
  }
  &.highlight {
    background: $primary;
  }
}
</style>