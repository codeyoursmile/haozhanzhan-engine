<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageComponent;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    // 获取页面的所有组件
    public function getByPageId($pageId)
    {
        $components = PageComponent::where('page_id', $pageId)
            ->orderBy('sort_order')
            ->get();
        return response()->json($components);
    }

    // 创建组件
    public function store(Request $request)
    {
        $validated = $request->validate([
            'page_id' => 'required|exists:pages,id',
            'component_type' => 'required|string|max:50',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'sort_order' => 'integer',
        ]);

        $component = PageComponent::create($validated);
        return response()->json($component, 201);
    }

    // 更新组件
    public function update(Request $request, $id)
    {
        $component = PageComponent::findOrFail($id);

        $validated = $request->validate([
            'component_type' => 'string|max:50',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'sort_order' => 'integer',
        ]);

        $component->update($validated);
        return response()->json($component);
    }

    // 删除组件
    public function destroy($id)
    {
        $component = PageComponent::findOrFail($id);
        $component->delete();
        return response()->json(null, 204);
    }

    // 批量更新排序
    public function updateSortOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:page_components,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        foreach ($request->items as $item) {
            PageComponent::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return response()->json(['message' => '排序更新成功']);
    }
}