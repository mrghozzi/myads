@php
    $level = $level ?? 0;
    $hasChildren = $category->children && $category->children->isNotEmpty();
    $topicCount = \App\Models\ForumTopic::where('cat', $category->id)->count();
    $catColor = $category->color ?: '#615dfa';
@endphp

<div class="forum-tree-node level-{{ $level }}" data-id="{{ $category->id }}" data-parent-id="{{ $category->parent_id ?? '' }}" data-name="{{ $category->name }}">
    <div class="forum-tree-item shadow-sm">
        <!-- Drag Handle -->
        <div class="forum-drag-handle" title="{{ __('messages.drag_to_reorder_categories') ?? 'Drag to reorder or nest' }}">
            <i class="feather-move"></i>
        </div>

        <!-- Icon Box -->
        <div class="forum-tree-icon" style="background: {{ $catColor }}; color: #fff;">
            <i class="fa {{ $category->icons ?: 'fa-comments' }}"></i>
        </div>

        <!-- Main Info -->
        <div class="forum-tree-content">
            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                <span class="forum-tree-title fw-bold text-dark fs-14">{{ $category->name }}</span>
                <span class="badge bg-light text-muted border fs-11">#{{ $category->id }}</span>
                @if($category->isRoot())
                    <span class="badge bg-soft-primary text-primary fs-11 rounded-pill">{{ __('messages.root_category') ?? 'Root Category' }}</span>
                @else
                    <span class="badge bg-soft-info text-info fs-11 rounded-pill">{{ __('messages.sub_forum') ?? 'Sub Forum' }}</span>
                @endif
                
                @if($category->visibility == 0)
                    <span class="badge bg-soft-success text-success fs-11 rounded-pill"><i class="feather-eye me-1"></i> {{ __('messages.everyone') }}</span>
                @elseif($category->visibility == 1)
                    <span class="badge bg-soft-warning text-warning fs-11 rounded-pill"><i class="feather-users me-1"></i> {{ __('messages.members_only') }}</span>
                @else
                    <span class="badge bg-soft-danger text-danger fs-11 rounded-pill"><i class="feather-shield me-1"></i> {{ __('messages.mods_only') }}</span>
                @endif
            </div>

            <div class="d-flex align-items-center gap-3 text-muted fs-12">
                <span><i class="fa fa-comments me-1"></i> <strong>{{ number_format($topicCount) }}</strong> {{ __('messages.topics') }}</span>
                @if($hasChildren)
                    <span><i class="feather-folder me-1 text-primary"></i> <strong>{{ $category->children->count() }}</strong> {{ __('messages.sub_forums') ?? 'Sub Forums' }}</span>
                @endif
                @if($category->txt)
                    <span class="text-truncate d-none d-md-inline opacity-75" style="max-width: 250px;" title="{{ $category->txt }}">{{ $category->txt }}</span>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="forum-tree-actions d-flex align-items-center gap-2">
            <!-- Add Sub Forum Quick Button -->
            <button type="button" class="btn btn-sm btn-soft-primary rounded-pill px-3 py-1 fs-12 fw-semibold btn-add-subforum" data-id="{{ $category->id }}" data-name="{{ $category->name }}" title="{{ __('messages.add_sub_forum') ?? 'Add Sub Forum' }}">
                <i class="feather-plus-circle me-1"></i>
                <span>{{ __('messages.sub_forum') ?? 'Sub Forum' }}</span>
            </button>

            <!-- Move / Make Root Button (if child) -->
            @if(!$category->isRoot())
                <button type="button" class="btn btn-sm btn-icon btn-light rounded-circle btn-make-root" data-id="{{ $category->id }}" title="{{ __('messages.root_category') ?? 'Make Root Category' }}">
                    <i class="feather-corner-up-left"></i>
                </button>
            @endif

            <!-- Edit Button -->
            <button type="button" class="btn btn-sm btn-icon btn-light-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}" title="{{ __('messages.edit') }}">
                <i class="feather-edit-2"></i>
            </button>

            <!-- Delete Button -->
            <button type="button" class="btn btn-sm btn-icon btn-light-danger rounded-circle" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal{{ $category->id }}" title="{{ __('messages.delete') }}">
                <i class="feather-trash-2"></i>
            </button>
        </div>
    </div>

    <!-- Nested Sub-Forums Container -->
    <div class="forum-tree-sublist {{ $hasChildren ? 'has-items' : 'is-empty' }}" data-parent-id="{{ $category->id }}">
        @if($hasChildren)
            @foreach($category->children as $child)
                @include('admin::admin.partials.forum_category_tree_node', ['category' => $child, 'level' => $level + 1])
            @endforeach
        @endif
        <div class="forum-tree-empty-dropzone {{ $hasChildren ? 'd-none' : '' }}">
            <i class="feather-corner-down-right me-1 text-muted"></i>
            <span>{{ __('messages.drag_to_reorder_categories') ?? 'Drop sub-forums here or click' }}</span>
            <a href="javascript:void(0)" class="btn-add-subforum text-primary fw-bold ms-1" data-id="{{ $category->id }}" data-name="{{ $category->name }}">+ {{ __('messages.add_sub_forum') ?? 'Add Sub Forum' }}</a>
        </div>
    </div>
</div>
