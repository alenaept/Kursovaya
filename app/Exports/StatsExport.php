<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class StatsExport implements FromArray, WithHeadings, ShouldAutoSize
{
    protected $stats;
    
    public function __construct($stats)
    {
        $this->stats = $stats;
    }
    
    public function array(): array
    {
        return [

            ['основные показатели', '', ''],
            ['Врачей', $this->stats['totalDoctors'] ?? 0, ''],
            ['Услуг', $this->stats['totalServices'] ?? 0, ''],
            ['Акций', $this->stats['totalOffers'] ?? 0, ''],
            ['Заявок', $this->stats['totalRequests'] ?? 0, ''],
            ['Отзывов', $this->stats['totalReviews'] ?? 0, ''],
            ['Записей', $this->stats['totalAppointments'] ?? 0, ''],
            ['Пациентов', $this->stats['totalUsers'] ?? 0, ''],
            ['', '', ''],
            

            ['Отзывы', '', ''],
            ['Опубликовано отзывов', $this->stats['publishedReviews'] ?? 0, ''],
            ['На модерации', $this->stats['unpublishedReviews'] ?? 0, ''],
            ['', '', ''],
            

            ['записи', '', ''],
            ['Записей сегодня', $this->stats['appointmentsToday'] ?? 0, ''],
            ['Записей за месяц', $this->stats['appointmentsThisMonth'] ?? 0, ''],
            ['', '', ''],
            
 
            ['Пациенты', '', ''],
            ['Новых пациентов за месяц', $this->stats['newUsersThisMonth'] ?? 0, ''],
            ['', '', ''],
            

            ['Самый загруженный врач', '', ''],
            ['Имя', $this->stats['busyDoctor']?->first_name ?? '-', ''],
            ['Фамилия', $this->stats['busyDoctor']?->last_name ?? '-', ''],
            ['Количество записей', $this->stats['busyDoctor']?->total ?? '0', ''],
            ['', '', ''],
            
  
            ['дата', date('d.m.Y H:i:s'), ''],
        ];
    }
    
    public function headings(): array
    {
        return [
            'Показатель',
            'Значение',
        ];
    }
    
    }
