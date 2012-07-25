<?php
/**
 * This file contains the abstact top class for all evaluators for tertairy functions
 * 
 * @package The-Datatank/universalfilter/interpreter/executers
 * @copyright (C) 2012 by iRail vzw/asbl
 * @license AGPLv3
 * @author Jeroen Penninck
 */
abstract class TertairyFunctionExecuter extends UniversalFilterNodeExecuter {
    
    private $filter;
    
    private $header;
    
    private $executer1;
    private $executer2;
    private $executer3;
    
    private $header1;
    private $header2;
    private $header3;
    
    public function initExpression(UniversalFilterNode $filter, Environment $topenv, IInterpreterControl $interpreter, $preferColumn){
        $this->filter = $filter;
        
        $this->executer1 = $interpreter->findExecuterFor($this->filter->getSource(0));
        $this->executer2 = $interpreter->findExecuterFor($this->filter->getSource(1));
        $this->executer3 = $interpreter->findExecuterFor($this->filter->getSource(2));
        
        //init down
        $this->executer1->initExpression($this->filter->getSource(0), $topenv, $interpreter, true);
        $this->executer2->initExpression($this->filter->getSource(1), $topenv, $interpreter, true);
        $this->executer3->initExpression($this->filter->getSource(2), $topenv, $interpreter, true);
        
        $this->header1 = $this->executer1->getExpressionHeader();
        $this->header2 = $this->executer2->getExpressionHeader();
        $this->header3 = $this->executer3->getExpressionHeader();
        
        //combined name
        $combinedName = $this->getName(
                $this->header1->getColumnNameById($this->header1->getColumnId()), 
                $this->header2->getColumnNameById($this->header2->getColumnId()),
                $this->header3->getColumnNameById($this->header3->getColumnId()));
        
        //column
        $cominedHeaderColumn = new UniversalFilterTableHeaderColumnInfo(array($combinedName));
        
        //single row?
        $isSingleRowByConstruction = 
                $this->header1->isSingleRowByConstruction() && 
                $this->header2->isSingleRowByConstruction() &&
                $this->header3->isSingleRowByConstruction();
        
        //new Header
        $this->header = new UniversalFilterTableHeader(array($cominedHeaderColumn), $isSingleRowByConstruction, true);
    }
    
    public function getExpressionHeader(){
        return $this->header;
    }
    
    public function evaluateAsExpression() {
        $table1content = $this->executer1->evaluateAsExpression();
        $table2content = $this->executer2->evaluateAsExpression();
        $table3content = $this->executer3->evaluateAsExpression();
        
        $idA = $this->header1->getColumnId();
        $idB = $this->header2->getColumnId();
        $idC = $this->header3->getColumnId();
        $finalid = $this->header->getColumnId();
        
        // TODO: Not really correct code, should also check two on two... (So if becomes even bigger)
        // But not really necesairy, just a check...
        if(
                !$this->header1->isSingleRowByConstruction() && 
                !$this->header2->isSingleRowByConstruction() &&
                !$this->header3->isSingleRowByConstruction() &&
                ($table1content->getRowCount()!=$table2content->getRowCount() || 
                 $table2content->getRowCount()!=$table3content->getRowCount())){
            throw new Exception("Columns differ in size");//Can that happen??????????
        }
        
        $rows=new UniversalFilterTableContent();
        
        $size=max(array(
            $table1content->getRowCount(), 
            $table2content->getRowCount(),
            $table3content->getRowCount()));
        
        //loop through all rows and evaluate the expression
        for ($i=0;$i<$size;$i++){
            $row=new UniversalFilterTableContentRow();
            
            //get the value for index i for both tables
            $valueA=null;
            $valueB=null;
            $valueC=null;
            if($table1content->getRowCount()>$i){
                $valueA=$table1content->getValue($idA, $i);
            }else{
                $valueA=$table1content->getCellValue($idA);
            }
            if($table2content->getRowCount()>$i){
                $valueB=$table2content->getValue($idB, $i);
            }else{
                $valueB=$table2content->getCellValue($idB);
            }
            if($table3content->getRowCount()>$i){
                $valueC=$table3content->getValue($idC, $i);
            }else{
                $valueC=$table3content->getCellValue($idC);
            }
            
            //evaluate
            $value = $this->doTertairyFunction($valueA, $valueB, $valueC);
            
            $row->defineValue($finalid, $value);
            
            $rows->addRow($row);
        }
        
        $table1content->tryDestroyTable();
        $table2content->tryDestroyTable();
        $table3content->tryDestroyTable();
        
        //return the result
        return $rows;
    }
    
    public function cleanUp(){
        $this->executer1->cleanUp();
        $this->executer2->cleanUp();
        $this->executer3->cleanUp();
    }
    
    
    
    public function getName($nameA, $nameB, $nameC){
        return $nameA." combined ".$nameA;
    }
    
    public function doBinaryFunction($valueA, $valueB, $valueC){
        return null;
    }
}
?>