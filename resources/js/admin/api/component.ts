import axios from 'axios';
import type { PageComponent, CreateComponentInput } from '../types/components';

export const componentApi = {
    // 获取页面的所有组件
    getByPageId: (pageId: number) => axios.get<PageComponent[]>(`/api/admin/pages/${pageId}/components`),
    
    // 创建组件
    create: (data: CreateComponentInput) => axios.post<PageComponent>('/api/admin/components', data),
    
    // 更新组件
    update: (id: number, data: Partial<CreateComponentInput>) => axios.put<PageComponent>(`/api/admin/components/${id}`, data),
    
    // 删除组件
    delete: (id: number) => axios.delete(`/api/admin/components/${id}`),
    
    // 批量更新排序
    updateSortOrder: (items: { id: number; sort_order: number }[]) => axios.post('/api/admin/components/sort', { items }),
};