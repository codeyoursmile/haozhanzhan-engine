import axios from 'axios';

export interface Site {
    id: number;
    site_name: string;
    site_logo: string | null;
    site_keywords: string | null;
    site_description: string | null;
    created_at: string;
    updated_at: string;
}

export const siteApi = {
    // 获取站点配置
    get: () => axios.get<Site>('/api/admin/site'),
    
    // 更新站点配置
    update: (data: Partial<Site>) => axios.put<Site>('/api/admin/site', data),
};