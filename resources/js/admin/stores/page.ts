import { defineStore } from 'pinia';
import { ref } from 'vue';
import { pageApi, type Page } from '../api/page';
import { ElMessage } from 'element-plus';

export const usePageStore = defineStore('page', () => {
    const pages = ref<Page[]>([]);
    const loading = ref(false);
    const currentPage = ref<Page | null>(null);

    // 获取页面列表
    const fetchPages = async () => {
        loading.value = true;
        try {
            const response = await pageApi.getList();
            pages.value = response.data;
        } catch (error) {
            ElMessage.error('获取页面列表失败');
        } finally {
            loading.value = false;
        }
    };

    // 获取单个页面
    const fetchPage = async (id: number) => {
        loading.value = true;
        try {
            const response = await pageApi.getDetail(id);
            currentPage.value = response.data;
        } catch (error) {
            ElMessage.error('获取页面详情失败');
        } finally {
            loading.value = false;
        }
    };

    // 创建页面
    const createPage = async (data: Partial<Page>) => {
        loading.value = true;
        try {
            const response = await pageApi.create(data);
            pages.value.unshift(response.data);
            ElMessage.success('创建成功');
            return response.data;
        } catch (error) {
            ElMessage.error('创建失败');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    // 更新页面
    const updatePage = async (id: number, data: Partial<Page>) => {
        loading.value = true;
        try {
            const response = await pageApi.update(id, data);
            const index = pages.value.findIndex(p => p.id === id);
            if (index !== -1) {
                pages.value[index] = response.data;
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

    // 删除页面
    const deletePage = async (id: number) => {
        loading.value = true;
        try {
            await pageApi.delete(id);
            pages.value = pages.value.filter(p => p.id !== id);
            ElMessage.success('删除成功');
        } catch (error) {
            ElMessage.error('删除失败');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    return {
        pages,
        loading,
        currentPage,
        fetchPages,
        fetchPage,
        createPage,
        updatePage,
        deletePage,
    };
});