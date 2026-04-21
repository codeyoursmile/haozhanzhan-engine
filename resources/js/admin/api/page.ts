import axios from 'axios';

export interface Page {
    id: number;
    site_id: number;
    title: string;
    slug: string;
    is_home: boolean;
    status: boolean;
    created_at: string;
    updated_at: string;
}

export const pageApi = {
    // 获取页面列表
    getList: () => axios.get<Page[]>('/api/admin/pages'),
    
    // 获取单个页面
    getDetail: (id: number) => axios.get<Page>(`/api/admin/pages/${id}`),
    
    // 创建页面
    create: (data: Partial<Page>) => axios.post<Page>('/api/admin/pages', data),
    
    // 更新页面
    update: (id: number, data: Partial<Page>) => axios.put<Page>(`/api/admin/pages/${id}`, data),
    
    // 删除页面
    delete: (id: number) => axios.delete(`/api/admin/pages/${id}`),
};