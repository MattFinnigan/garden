<template>
  <div class="table-component">
    <div v-if="header" class="table-header">
      <slot name="header"></slot>
    </div>
    <div v-if="prefix" class="table-prefix">
      <slot name="prefix"></slot>
    </div>
    <table v-if="rows.length">
      <thead>
        <tr>
          <th v-for="header in headers" :key="header" :style="header.width ? 'width:' + header.width : ''">{{ header.label }}</th>
          <th v-if="actions" style="width:110px"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(row, i) in rows" :key="row.id || i">
          <td v-for="header in headers" :key="header.key">
            <Button v-if="header.action" class="sm icon secondary" @click="$emit(header.action, row)">
              <Icon :name="header.icon"></Icon>
            </Button>
            <span v-else v-html="header.key === 'num' ? i + 1 : row[header.key]"></span>
          </td>
          <td v-if="actions" class="action-buttons">
            <Button v-if="actions.view" classes="sm secondary3 icon" @click="$emit('view', row, i)"><Icon name="view"></Icon></Button>
            <Button v-if="actions.edit" classes="sm secondary3 icon" @click="$emit('edit', row, i)"><Icon name="edit"></Icon></Button>
            <Button v-if="actions.delete" classes="sm danger icon" @click="$emit('delete', row, i)"><Icon name="delete"></Icon></Button>
          </td>
        </tr>
      </tbody>
    </table>
    <div v-if="rows.length" class="mobile-table">
      <div v-for="(row, i) in rows" :key="row.id || i" class="row">
        <div v-for="header in headers" :key="header.key" class="header">
          <div class="row-data">
            <span v-html="header.key === 'num' ? ('#' + i + 1) : row[header.key]"></span>
          </div>
        </div>
        <div v-if="actions" class="action-buttons">
          <Button v-if="actions.view" classes="sm secondary3 icon" @click="$emit('view', row, i)"><Icon name="view"></Icon></Button>
          <Button v-if="actions.edit" classes="sm secondary3 icon" @click="$emit('edit', row, i)"><Icon name="edit"></Icon></Button>
          <Button v-if="actions.delete" classes="sm danger icon" @click="$emit('delete', row, i)"><Icon name="delete"></Icon></Button>
        </div>
      </div>
    </div>
    <div v-if="!rows.length && empty"><slot name="empty"></slot></div>
  </div>
</template>

<script>
export default {
  emits: ['view', 'edit', 'delete', 'openImageModal'],
  name: 'TableComponent',
  props: {
    headers: Array,
    rows: Array,
    actions: Object
  },
  computed: {
    header() {
      return !!this.$slots.header
    },
    prefix() {
      return !!this.$slots.prefix
    },
    empty() {
      return !!this.$slots.empty
    }
  }
}
</script>

<style scoped lang="scss">
.table-component {
  .action-buttons {
    text-align: right;
    Button {
      margin-left: 0.5em;
    }
  }
  .table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  table {
    display: none;
    width: 100%;
    border-collapse: collapse;
    border: 1px solid $grey-500;
    border-radius: 0.5em;
    thead {
      background-color: $grey-500;
      border-bottom: 1px solid $grey-500;
      th {
        padding: 10px;
        text-align: left;
        font-size: $fsSmall;
      }
    }
    tbody {
      tr {
        &:nth-child(even) {
          background-color: $grey-200;
        }
        td {
          padding: 10px;
          span {
            font-size: $fsSmall;
          }
        }
      }
    }
  }
  .mobile-table {
    display: block;
    .row {
      padding: 10px;
      border: 1px solid $grey-500;
      .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 2px 0;
        .row-data {
          flex: 1;
          font-size: $fsSmall;
        }
      }
    }
    > :nth-child(even) {
      background-color: $grey-200;
    }
  }
  @include device (desktop, 'all') {
    table {
      display: table;
    }
    .mobile-table {
      display: none;
    }
  }
}
</style>