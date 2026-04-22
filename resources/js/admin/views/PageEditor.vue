<template>
    <div class="editor-container">
        <!-- 左侧：组件库 -->
        <div class="editor-sidebar">
            <div class="sidebar-title">组件库</div>
            <div class="component-list">
                <div
                    v-for="item in componentLibrary"
                    :key="item.type"
                    class="component-item"
                    draggable="true"
                    @dragstart="onDragStart($event, item)"
                >
                    {{ item.name }}
                </div>
            </div>
        </div>

        <!-- 中间：画布 -->
        <div class="editor-canvas">
            <div class="canvas-title">页面画布</div>
            <div
                ref="canvasRef"
                class="canvas-content"
                @dragover.prevent
                @drop="onDrop"
            >
                <div
                    v-for="comp in componentStore.components"
                    :key="comp.id"
                    class="canvas-component"
                    :data-id="comp.id"
                >
                    <div class="component-header">
                        <span>{{ getComponentName(comp.component_type) }}</span>
                        <el-button type="danger" link size="small" @click="componentStore.deleteComponent(comp.id)">
                            删除
                        </el-button>
                    </div>
                    <div class="component-preview">
                        预览: {{ getComponentName(comp.component_type) }}
                    </div>
                </div>
                <p v-if="componentStore.components.length === 0" class="empty-tip">
                    从左侧拖拽组件到这里
                </p>
            </div>
        </div>

        <!-- 右侧：属性面板 -->
        <div class="editor-property">
            <div class="property-title">属性面板</div>
            <div class="property-content">
                <p class="empty-tip">点击组件编辑属性</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import Sortable from 'sortablejs';
import { useComponentStore } from '../stores/component';
import type { ComponentType } from '../types/components';

const route = useRoute();
const pageId = Number(route.params.id);
const componentStore = useComponentStore();
const canvasRef = ref<HTMLElement | null>(null);
let sortableInstance: Sortable | null = null;

// 组件库列表
const componentLibrary = ref([
    { type: 'banner' as ComponentType, name: '横幅 Banner' },
    { type: 'text' as ComponentType, name: '纯文本' },
    { type: 'image' as ComponentType, name: '图片' },
    { type: 'multi_col' as ComponentType, name: '多列布局' },
    { type: 'map' as ComponentType, name: '地图' },
    { type: 'contact_form' as ComponentType, name: '联系表单' },
]);

// 拖拽开始
const onDragStart = (evt: DragEvent, component: { type: ComponentType; name: string }) => {
    if (evt.dataTransfer) {
        evt.dataTransfer.setData('text/plain', JSON.stringify({
            type: component.type,
            name: component.name,
        }));
        evt.dataTransfer.effectAllowed = 'copy';
    }
};

// 放置组件
const onDrop = async (evt: DragEvent) => {
    evt.preventDefault();
    const rawData = evt.dataTransfer?.getData('text/plain');
    if (!rawData) return;
    
    const component = JSON.parse(rawData);
    const newSortOrder = componentStore.components.length;
    
    await componentStore.addComponent({
        page_id: pageId,
        component_type: component.type,
        content: {},
        settings: {},
        sort_order: newSortOrder,
    });
};

// 初始化画布排序
const initSortable = () => {
    if (!canvasRef.value) return;
    
    sortableInstance = new Sortable(canvasRef.value, {
        animation: 150,
        handle: '.canvas-component',
        onEnd: async () => {
            // 获取排序后的组件顺序
            const items = canvasRef.value?.querySelectorAll('.canvas-component');
            if (!items) return;
            
            const sortedItems = Array.from(items).map((el, index) => ({
                id: Number(el.getAttribute('data-id')),
                sort_order: index,
            }));
            
            await componentStore.updateSortOrder(sortedItems);
        },
    });
};

// 销毁排序实例
const destroySortable = () => {
    if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
    }
};

// 获取组件显示名称
const getComponentName = (type: ComponentType) => {
    const map: Record<ComponentType, string> = {
        banner: '横幅 Banner',
        text: '纯文本',
        image: '图片',
        multi_col: '多列布局',
        map: '地图',
        contact_form: '联系表单',
    };
    return map[type] || type;
};

onMounted(async () => {
    await componentStore.fetchComponents(pageId);
    await nextTick();
    initSortable();
});
</script>

<style scoped>
.editor-container {
    display: flex;
    height: calc(100vh - 120px);
    gap: 16px;
}

.editor-sidebar {
    width: 260px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.editor-canvas {
    flex: 1;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.editor-property {
    width: 320px;
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.sidebar-title,
.canvas-title,
.property-title {
    padding: 16px;
    font-weight: bold;
    border-bottom: 1px solid #e8e8e8;
    background: #fafafa;
}

.component-list {
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.component-item {
    padding: 12px;
    background: #f5f5f5;
    border-radius: 6px;
    cursor: grab;
    text-align: center;
    transition: all 0.2s;
}

.component-item:hover {
    background: #e6f7ff;
    border-color: #1890ff;
}

.canvas-content {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
    min-height: 200px;
}

.canvas-component {
    background: #fafafa;
    border: 1px solid #e8e8e8;
    border-radius: 6px;
    margin-bottom: 12px;
    overflow: hidden;
    cursor: grab;
}

.component-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 12px;
    background: #f5f5f5;
    border-bottom: 1px solid #e8e8e8;
}

.component-preview {
    padding: 20px;
    text-align: center;
    color: #666;
}

.empty-tip {
    text-align: center;
    color: #999;
    padding: 40px;
}

.property-content {
    flex: 1;
    padding: 16px;
    overflow-y: auto;
}
</style>