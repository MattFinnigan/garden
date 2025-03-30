<template>
  <div :class="['list-item', {highlight}]">
    <div class="list-item-wrapper">
      <div class="img-contain">
        <slot name="image"></slot>
      </div>
        <div class="details-actions">
          <div class="title-contain">
          <strong><slot name="title"></slot></strong>
          <slot name="description"></slot>
        </div>
      </div>
      <div class="actions">
        <slot name="actions"></slot>
      </div>
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
    .details-actions {
      flex: 1;
      display: flex;
      .title-contain, .title-contain * {
        font-size: $fsSmall;
        :deep(p) {
          margin: 0;
        }
      }
    }
    .actions {
      display: flex;
      align-items: flex-end;
    }
  }
  &.highlight {
    background: $accent;
  }
}
</style>