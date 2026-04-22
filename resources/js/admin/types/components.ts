// 组件类型枚举
export type ComponentType = 
    | 'banner'      // 横幅
    | 'text'        // 纯文本
    | 'image'       // 图片
    | 'multi_col'   // 多列布局
    | 'map'         // 地图
    | 'contact_form'; // 联系表单

// 组件内容接口
export interface ComponentContent {
    title?: string;      // 标题
    subtitle?: string;   // 副标题
    text?: string;       // 文本内容
    image_url?: string;  // 图片地址
    link_url?: string;   // 链接地址
    columns?: Column[];  // 多列布局的列数据
    address?: string;    // 地图地址
    email?: string;      // 联系邮箱
    phone?: string;      // 联系电话
}

// 多列布局的单列数据
export interface Column {
    title: string;
    content: string;
    image_url?: string;
}

// 样式设置接口
export interface ComponentSettings {
    background_color?: string;  // 背景色
    text_color?: string;        // 文字颜色
    padding?: string;           // 内边距
    margin?: string;            // 外边距
    width?: string;             // 宽度
    height?: string;            // 高度
    align?: 'left' | 'center' | 'right';  // 对齐方式
}

// 组件完整数据结构
export interface PageComponent {
    id: number;
    page_id: number;
    component_type: ComponentType;
    content: ComponentContent;
    settings: ComponentSettings;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

// 创建组件的输入类型（不含 id 和时间戳）
export type CreateComponentInput = Omit<PageComponent, 'id' | 'created_at' | 'updated_at'>;