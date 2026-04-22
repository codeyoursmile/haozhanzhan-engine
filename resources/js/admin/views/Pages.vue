<template>
    <div class="pages-container">
        <el-card>
            <template #header>
                <div class="card-header">
                    <span>页面管理</span>
                    <el-button type="primary" size="small" @click="handleCreate">
                        <el-icon><Plus /></el-icon>
                        新建页面
                    </el-button>
                </div>
            </template>

            <el-table :data="pageStore.pages" v-loading="pageStore.loading" stripe>
                <el-table-column prop="id" label="ID" width="80" />
                <el-table-column prop="title" label="页面标题" />
                <el-table-column prop="slug" label="访问别名" />
                <el-table-column prop="is_home" label="是否首页" width="100">
                    <template #default="{ row }">
                        <el-tag :type="row.is_home ? 'success' : 'info'">
                            {{ row.is_home ? '是' : '否' }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="status" label="状态" width="100">
                    <template #default="{ row }">
                        <el-tag :type="row.status ? 'success' : 'danger'">
                            {{ row.status ? '已发布' : '草稿' }}
                        </el-tag>
                    </template>
                </el-table-column>
                <el-table-column prop="updated_at" label="更新时间" width="180" />
                <el-table-column label="操作" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)">
                            编辑
                        </el-button>
                        <el-button type="danger" link @click="handleDelete(row)">
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-card>

        <!-- 新建/编辑对话框 -->
        <el-dialog
            v-model="dialogVisible"
            :title="dialogTitle"
            width="500px"
            @close="handleDialogClose"
        >
            <el-form :model="form" :rules="rules" ref="formRef" label-width="100px">
                <el-form-item label="页面标题" prop="title">
                    <el-input v-model="form.title" placeholder="请输入页面标题" />
                </el-form-item>
                <el-form-item label="访问别名" prop="slug">
                    <el-input v-model="form.slug" placeholder="例如: about" />
                </el-form-item>
                <el-form-item label="是否首页" prop="is_home">
                    <el-switch v-model="form.is_home" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-switch
                        v-model="form.status"
                        active-value="1"
                        inactive-value="0"
                        active-text="发布"
                        inactive-text="草稿"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="dialogVisible = false">取消</el-button>
                <el-button type="primary" @click="handleSubmit" :loading="submitting">
                    确定
                </el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { ElMessageBox } from 'element-plus';
import { Plus } from '@element-plus/icons-vue';
import { usePageStore } from '../stores/page';
import { useRouter } from 'vue-router';

const pageStore = usePageStore();
const dialogVisible = ref(false);
const dialogTitle = ref('');
const submitting = ref(false);
const formRef = ref();
const isEdit = ref(false);
const editId = ref<number | null>(null);

const form = reactive({
    title: '',
    slug: '',
    is_home: false,
    status: false,
});

const rules = {
    title: [{ required: true, message: '请输入页面标题', trigger: 'blur' }],
    slug: [{ required: true, message: '请输入访问别名', trigger: 'blur' }],
};

const handleCreate = () => {
    isEdit.value = false;
    editId.value = null;
    dialogTitle.value = '新建页面';
    resetForm();
    dialogVisible.value = true;
};

const router = useRouter();
const handleEdit = (row: any) => {
    router.push(`/admin/pages/${row.id}/edit`);
};

const handleDelete = (row: any) => {
    ElMessageBox.confirm(`确定要删除页面「${row.title}」吗？`, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning',
    }).then(async () => {
        await pageStore.deletePage(row.id);
        await pageStore.fetchPages();
    }).catch(() => {});
};

const handleSubmit = async () => {
    if (!formRef.value) return;
    
    await formRef.value.validate(async (valid: boolean) => {
        if (!valid) return;

        submitting.value = true;
        try {
            if (isEdit.value && editId.value) {
                await pageStore.updatePage(editId.value, form);
            } else {
                await pageStore.createPage(form);
            }
            dialogVisible.value = false;
            await pageStore.fetchPages();
        } finally {
            submitting.value = false;
        }
    });
};

const handleDialogClose = () => {
    resetForm();
    formRef.value?.clearValidate();
};

const resetForm = () => {
    form.title = '';
    form.slug = '';
    form.is_home = false;
    form.status = false;
};

onMounted(() => {
    pageStore.fetchPages();
});
</script>

<style scoped>
.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
</style>