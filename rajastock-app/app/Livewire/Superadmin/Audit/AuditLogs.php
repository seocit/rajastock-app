<?php

namespace App\Livewire\Superadmin\Audit;

use App\Models\AuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $event = '';
    public $selectedLog = null;
    public $showModal = false;

    protected $queryString = ['search', 'event'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEvent()
    {
        $this->resetPage();
    }

    public function viewDetail($id)
    {
        $this->selectedLog = AuditLog::find($id);
        $this->showModal = true;
    }

    public function render()
    {
         $logs = AuditLog::query()
            ->when($this->search, function ($q) {
                $q->where('model', 'like', '%'.$this->search.'%')
                  ->orWhere('event', 'like', '%'.$this->search.'%');
                 
            })
            ->when($this->event, function ($q) {
                $q->where('event', $this->event);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.superadmin.audit.audit-logs',
            [
                'logs' => $logs,
            ]
        );
    }
}
