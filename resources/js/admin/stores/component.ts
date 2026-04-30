import { defineStore } from 'pinia';
import { ref } from 'vue';
import { componentApi } from '../api/component';
import type { PageComponent, CreateComponentInput } from '../types/components';
import { ElMessage } from 'element-plus';

export const useComponentStore = defineStore('component', () => {
    const components = ref<PageComponent[]>([]);
    const loading = ref(false);

    // 加载页面的组件列表
    const fetchComponents = async (pageId: number) => {
        loading.value = true;
        try {
            const response = await componentApi.getByPageId(pageId);
            // 按 sort_order 排序
            components.value = response.data.sort((a, b) => a.sort_order - b.sort_order);
        } catch (error) {
            ElMessage.error('加载组件失败');
        } finally {
            loading.value = false;
        }
    };

    // 添加组件
    const addComponent = async (data: CreateComponentInput) => {
        loading.value = true;
        try {
            const response = await componentApi.create(data);
            components.value.push(response.data);
            ElMessage.success('添加成功');
            return response.data;
        } catch (error) {
            ElMessage.error('添加失败');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // 更新组件
    const updateComponent = async (id: number, data: Partial<CreateComponentInput>) => {
        loading.value = true;
        try {
            const response = await componentApi.update(id, data);
            const index = components.value.findIndex(c => c.id === id);
            if (index !== -1) {
                components.value[index] = response.data;
            }
            ElMessage.success('更新成功');
            return response.data;
        } catch (error) {
            ElMessage.error('更新失败');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // 删除组件（返回被删除的组件ID，供调用方清空选中状态）
    const deleteComponent = async (id: number): Promise<number> => {
        loading.value = true;
        try {
            await componentApi.delete(id);
            components.value = components.value.filter(c => c.id !== id);
            ElMessage.success('删除成功');
            return id;  // 返回被删除的组件ID
        } catch (error) {
            ElMessage.error('删除失败');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // 更新排序
    const updateSortOrder = async (items: { id: number; sort_order: number }[]) => {
        try {
            await componentApi.updateSortOrder(items);
            // 更新本地排序
            items.forEach(item => {
                const component = components.value.find(c => c.id === item.id);
                if (component) {
                    component.sort_order = item.sort_order;
                }
            });
            components.value.sort((a, b) => a.sort_order - b.sort_order);
        } catch (error) {
            ElMessage.error('更新排序失败');
            throw error;
        }
    };

    return {
        components,
        loading,
        fetchComponents,
        addComponent,
        updateComponent,
        deleteComponent,
        updateSortOrder,
    };
});