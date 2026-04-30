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
            <div class="canvas-title">
                <span>页面画布</span>
                <el-button type="primary" size="small" @click="savePage" :loading="savingPage">
                    保存页面
                </el-button>
            </div>
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
                    :class="{ 'selected': selectedId === comp.id }"
                    :data-id="comp.id"
                    @click="selectComponent(comp)"
                >
                    <div class="component-header">
                        <span>{{ getComponentName(comp.component_type) }}</span>
                        <el-button type="danger" link size="small" @click.stop="handleDeleteComponent(comp)">
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
                <div v-if="selectedComponent">
                    <el-form label-width="80px" :model="editForm">
                        <el-form-item label="组件类型">
                            <span>{{ getComponentName(selectedComponent.component_type) }}</span>
                        </el-form-item>
                        <el-form-item label="标题">
                            <el-input v-model="editForm.title" placeholder="请输入标题" />
                        </el-form-item>
                        <el-form-item label="副标题" v-if="selectedComponent.component_type === 'banner'">
                            <el-input v-model="editForm.subtitle" placeholder="请输入副标题" />
                        </el-form-item>
                        <el-form-item label="内容" v-if="selectedComponent.component_type === 'text'">
                            <el-input type="textarea" v-model="editForm.text" rows="4" placeholder="请输入内容" />
                        </el-form-item>
                        <el-form-item label="图片地址" v-if="['banner', 'image'].includes(selectedComponent.component_type)">
                            <el-input v-model="editForm.image_url" placeholder="例如: /images/banner.jpg" />
                        </el-form-item>
                        <el-form-item label="链接地址" v-if="['banner', 'image'].includes(selectedComponent.component_type)">
                            <el-input v-model="editForm.link_url" placeholder="例如: /about" />
                        </el-form-item>
                        <el-form-item>
                            <el-button type="primary" @click="saveComponent" :loading="saving">保存</el-button>
                        </el-form-item>
                    </el-form>
                </div>
                <p v-else class="empty-tip">点击组件编辑属性</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, nextTick, watch } from 'vue';
import { useRoute } from 'vue-router';
import { ElMessage } from 'element-plus';
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

// 选中状态和表单变量
const selectedComponent = ref<any>(null);
const selectedId = ref<number | null>(null);
const editForm = ref({
    title: '',
    subtitle: '',
    text: '',
    image_url: '',
    link_url: '',
    columns: [] as any[],
});
const saving = ref(false);
const savingPage = ref(false);
const columnCount = ref(3);

// 初始化列数据
const initColumns = () => {
    const cols = [];
    for (let i = 0; i < columnCount.value; i++) {
        cols.push({ image_url: '', title: '', content: '' });
    }
    editForm.value.columns = cols;
};

const resizeColumns = () => {
    initColumns();
};

// 选中组件
const selectComponent = (comp: any) => {
    selectedComponent.value = comp;
    selectedId.value = comp.id;
};

// 删除组件（并清空选中状态）
const handleDeleteComponent = async (comp: any) => {
    try {
        await componentStore.deleteComponent(comp.id);
        // 如果删除的是当前选中的组件，清空选中状态
        if (selectedId.value === comp.id) {
            selectedComponent.value = null;
            selectedId.value = null;
        }
    } catch (error) {
        // 错误已在 store 中处理
    }
};

// 监听选中组件变化，自动填充表单
watch(selectedComponent, (newVal) => {
    if (newVal && newVal.content) {
        const type = newVal.component_type;
        const content = newVal.content;
        
        if (type === 'multi_col') {
            const cols = content.columns || [];
            columnCount.value = cols.length || 3;
            editForm.value.columns = cols.length ? [...cols] : [
                { image_url: '', title: '列1', content: '内容1' },
                { image_url: '', title: '列2', content: '内容2' },
                { image_url: '', title: '列3', content: '内容3' },
            ];
        } else if (type === 'contact_form') {
            editForm.value.title = content.title || '';
            editForm.value.subtitle = content.subtitle || '';
            editForm.value.text = '';
            editForm.value.image_url = '';
            editForm.value.link_url = '';
        } else if (type === 'banner') {
            editForm.value.title = content.title || '';
            editForm.value.subtitle = content.subtitle || '';
            editForm.value.image_url = content.image_url || '';
            editForm.value.link_url = content.link_url || '';
            editForm.value.text = '';
        } else if (type === 'text') {
            editForm.value.title = content.title || '';
            editForm.value.text = content.text || '';
            editForm.value.subtitle = '';
            editForm.value.image_url = '';
            editForm.value.link_url = '';
        } else if (type === 'image') {
            editForm.value.title = content.title || '';
            editForm.value.image_url = content.image_url || '';
            editForm.value.link_url = content.link_url || '';
            editForm.value.subtitle = '';
            editForm.value.text = '';
        } else {
            editForm.value.title = content.title || '';
            editForm.value.subtitle = '';
            editForm.value.text = '';
            editForm.value.image_url = '';
            editForm.value.link_url = '';
        }
    } else {
        // 重置表单
        editForm.value = {
            title: '',
            subtitle: '',
            text: '',
            image_url: '',
            link_url: '',
            columns: [],
        };
        columnCount.value = 3;
    }
}, { immediate: true });

// 保存组件属性（按类型过滤字段）
const saveComponent = async () => {
    if (!selectedComponent.value) return;
    saving.value = true;
    try {
        let contentData: any = {};
        const type = selectedComponent.value.component_type;
        
        if (type === 'multi_col') {
            contentData = { columns: editForm.value.columns };
        } else if (type === 'contact_form') {
            contentData = {
                title: editForm.value.title,
                subtitle: editForm.value.subtitle,
            };
        } else if (type === 'banner') {
            contentData = {
                title: editForm.value.title,
                subtitle: editForm.value.subtitle,
                image_url: editForm.value.image_url,
                link_url: editForm.value.link_url,
            };
        } else if (type === 'text') {
            contentData = {
                title: editForm.value.title,
                text: editForm.value.text,
            };
        } else if (type === 'image') {
            contentData = {
                title: editForm.value.title,
                image_url: editForm.value.image_url,
                link_url: editForm.value.link_url,
            };
        } else {
            contentData = { title: editForm.value.title };
        }
        
        await componentStore.updateComponent(selectedComponent.value.id, {
            content: contentData,
        });
        selectedComponent.value.content = contentData;
        ElMessage.success('组件保存成功');
    } catch (error) {
        ElMessage.error('组件保存失败');
    } finally {
        saving.value = false;
    }
};

// 保存页面
const savePage = async () => {
    savingPage.value = true;
    try {
        await new Promise(resolve => setTimeout(resolve, 500));
        ElMessage.success('页面保存成功');
    } catch (error) {
        ElMessage.error('页面保存失败');
    } finally {
        savingPage.value = false;
    }
};

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
.sidebar-title, .canvas-title, .property-title {
    padding: 16px;
    font-weight: bold;
    border-bottom: 1px solid #e8e8e8;
    background: #fafafa;
}
.canvas-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    transition: all 0.2s;
}
.canvas-component.selected {
    border: 2px solid #1890ff;
    box-shadow: 0 0 0 2px rgba(24,144,255,0.2);
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