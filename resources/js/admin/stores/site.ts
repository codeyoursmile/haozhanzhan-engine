import { defineStore } from 'pinia';
import { ref } from 'vue';
import { siteApi, type Site } from '../api/site';
import { ElMessage } from 'element-plus';

export const useSiteStore = defineStore('site', () => {
    const site = ref<Site | null>(null);
    const loading = ref(false);

    // 获取站点配置
    const fetchSite = async () => {
        loading.value = true;
        try {
            const response = await siteApi.get();
            site.value = response.data;
        } catch (error) {
            ElMessage.error('获取站点配置失败');
        } finally {
            loading.value = false;
        }
    };

    // 更新站点配置
    const updateSite = async (data: Partial<Site>) => {
        loading.value = true;
        try {
            const response = await siteApi.update(data);
            site.value = response.data;
            ElMessage.success('保存成功');
            return response.data;
        } catch (error) {
            ElMessage.error('保存失败');
            throw error;
        } finally {
            loading.value = false;
        }
    };

    return {
        site,
        loading,
        fetchSite,
        updateSite,
    };
});