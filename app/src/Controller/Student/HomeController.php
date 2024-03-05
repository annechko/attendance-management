<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Repository\AttendanceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/student')]
class HomeController extends AbstractStudentController
{
    #[Route('', name: 'student_home')]
    public function index(
        AttendanceRepository $attendanceRepository,
        ChartBuilderInterface $chartBuilder,
        ): Response
    {
        $student = $this->getCurrentStudent();
        
        $labelSubjects = [];
        $valuesPresentRate=[];
        $valuesAbsentRate=[];
        $valuesExcuseRate=[];

        foreach ($student->getIntake()?->getPeriods() ?? [] as $period){
            foreach($period->getPeriodToSubjects() as $periodToSubject){

                array_push($labelSubjects, $periodToSubject->getSubject()->getCode());
                
                $presentCount=$attendanceRepository->getAttendanceCountPresent($student,$periodToSubject->getSubject());
                $absentCount=$attendanceRepository->getAttendanceCountAbsent($student,$periodToSubject->getSubject());
                $excuseCount=$attendanceRepository->getAttendanceCountExcuse($student,$periodToSubject->getSubject());
                
                $totalNumberOfLessons = $attendanceRepository->getAttendanceCount($student,$periodToSubject->getSubject());
                
                $presentRate=$totalNumberOfLessons > 0 ? ($presentCount / $totalNumberOfLessons) * 100 : 0;
                $absentRate=$totalNumberOfLessons > 0 ? ($absentCount / $totalNumberOfLessons) * 100 : 0;
                $excuseRate=$totalNumberOfLessons > 0 ? ($excuseCount / $totalNumberOfLessons) * 100 : 0;

                
                array_push($valuesPresentRate,number_format($presentRate,2,'.',''));
                array_push($valuesAbsentRate,number_format($absentRate,2,'.',''));
                array_push($valuesExcuseRate,number_format($excuseRate,2,'.',''));

            }
        }

        $chart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels'=>$labelSubjects,
            'datasets'=>[
                [   
                    'label'=>'Present Rate %',
                    'data'=>$valuesPresentRate,
                    'backgroundColor'=>['rgb(3, 144, 77,.4)'],
                    'borderColor'=>['#198753'],
                    'borderWidth'=>1,
                ],
                [   
                    'label'=>'Excused Rate %',
                    'data'=>$valuesExcuseRate,
                    'backgroundColor'=>['rgb(108, 117, 125,.4)'],
                    'borderColor'=>['#6C757D'],
                    'borderWidth'=>1,
                ],
                [   
                    'label'=>'Absent Rate %',
                    'data'=>$valuesAbsentRate,
                    'backgroundColor'=>['rgb(220, 53, 69,.4)'],
                    'borderColor'=>['#DC3545'],
                    'borderWidth'=>1,
                ],
            ],
        ]);
        $chart->setOptions([
            'indexAxis'=>'x',
            'maintainAspectRatio'=>false,
            'plugins'=>[
                'title'=>[
                    'display'=>true,
                    'text'=>'Student Attendance Rate per Subject',
                    'font'=>[
                        'size'=>16,
                    ]
                ],
                'legend'=>[
                    'position'=>'top',
                    'align'=>'center',
                ],
            ],
            'responsive'=>true,
            'scales'=>[
                'y'=>[
                    'stacked'=>true,
                    'max'=>100,
                    'title'=>[
                        'display'=>true,
                        'text'=>'Attendance Rate (%)',
                        'padding'=>20,
                        'font'=>[
                            'size'=>14,
                        ]
                    ],
                ],
                'x'=>[
                    'stacked'=>true,
                    'title'=>[
                        'display'=>true,
                        'text'=>'Subject Codes',
                        'padding'=>20,
                        'font'=>[
                            'size'=>14,
                        ]
                    ],
                ],
            ],
        ]);

        return $this->render('student/index.html.twig', [
            'chart'=>$chart,
            'student'=> $student,
        ]);

    }
}
