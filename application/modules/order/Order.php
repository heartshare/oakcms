<?php
namespace app\modules\order;

use app\modules\order\models\Order as OrderModel;
use app\modules\order\models\Element;
use yii\base\Component;
use yii\db\Query;
use yii;

class Order extends Component
{
    private $organization_id;
    private $is_assigment = false;

    public $payedStatus = 'payed';
    public $halfpayedStatus = 'halfpayed';

    public function init()
    {
        parent::init();
    }

    private function buildQuery($query)
    {
        if($this->organization_id) {
            $query->andWhere('(organization_id IS NULL OR organization_id = :organization_id)', [':organization_id' => $this->organization_id]);
        }

        if($this->is_assigment) {
            $query->andWhere('order.is_assigment = 1');
        } else {
            $query->andWhere('(order.is_assigment IS NULL OR order.is_assigment != 1)');
        }

        return $query;
    }

    private function orderQuery()
    {
        $return = (new Query())->from('order');

        return $this->buildQuery($return);
    }

    private function orderFinder()
    {
        $return = OrderModel::find();

        return $this->buildQuery($return);
    }

    public function resetConditions()
    {
        $this->organization_id = null;
        $this->is_assigment = false;

        return $this;
    }

    public function setOrganization($organization_id)
    {
        $this->organization_id = $organization_id;

        return $this;
    }

    public function assigment()
    {
        $this->is_assigment = true;

        return $this;
    }

    public function get($id)
    {
        return OrderModel::findOne($id);
    }

    public function getOrdersByDatePeriod($dateStart, $dateStop, $where = null)
    {

        if($dateStop == '0000-00-00 00:00:00' | empty($dateStop)) {
            $dateStop = date('Y-m-d H:i:s');
        }

        $query = $this->orderFinder()
            ->andWhere('date >= :dateStart AND date <= :dateStop', [':dateStart' => $dateStart, ':dateStop' => $dateStop]);

        if($where) {
            $query->andWhere($where);
        }

        $this->resetConditions();

        return $query->all();
    }

    public function getStatInMoth($month = null, $where = null)
    {
        if(!$month) {
            $month = date('Y-m');
        }

        $query = $this->orderQuery();
        $query->addSelect(['sum(cost) as total, sum(count) as count_elements, COUNT(DISTINCT id) as count_orders'])
                ->from(['order'])
                ->andWhere('DATE_FORMAT(date, "%Y-%m") = :date', [':date' => $month]);

        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }

    public function getStatByDate($date, $where = null)
    {
        $query = $this->orderQuery();

        $query->addSelect(['sum(cost) as total, sum(count) as count_elements, COUNT(DISTINCT id) as count_orders'])
                ->from(['order'])
                ->andWhere(' DATE_FORMAT(date, "%Y-%m-%d") = :date', [':date' => $date]);

        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }

    public function getStatByDatePeriod($dateStart, $dateStop = null, $where = null)
    {
        if($dateStop == '0000-00-00 00:00:00' | empty($dateStop)) {
            $dateStop = date('Y-m-d H:i:s');
        }

        $query = $this->orderQuery();
        $query->addSelect(['sum(cost) as total, sum(count) as count_elements, COUNT(DISTINCT id) as count_orders'])
                ->from(['order'])
                ->andWhere('date >= :dateStart AND date <= :dateStop', [':dateStart' => $dateStart, ':dateStop' => $dateStop]);

        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }

    public function getStatByModelAndDatePeriod($model, $dateStart, $dateStop, $where = null)
    {
        if($dateStop == '0000-00-00 00:00:00' | empty($dateStop)) {
            $dateStop = date('Y-m-d H:i:s');
        }

        $query = $this->orderQuery();
        $query->addSelect(['sum(e.count*e.price) as total, sum(e.count) as count_elements, COUNT(DISTINCT order_id) as count_orders'])
                ->from (['order_element e'])
                ->leftJoin('order', 'order.id = e.order_id')
                ->andWhere('e.is_assigment != 1')
                ->andWhere('order.date >= :dateStart AND order.date <= :dateStop', [':dateStart' => $dateStart, ':dateStop' => $dateStop])
                ->andWhere(['e.model' => $model]);

        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }

    public function setStatus($orderId, $status)
    {
        if ($orderId && $status) {
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand()->update('order', ['status' => $status], "id = $orderId");
            return $result = $command->execute();
        }
    }

    public function pushCartElements($id)
    {
        if($elements = yii::$app->cart->elements) {

            if($model = $this->get($id)) {
                foreach($elements as $element) {
                    $count = $element->getCount();

                    $orderElementModel = new Element;
                    $orderElementModel->order_id = $id;
                    $orderElementModel->is_assigment = $model->is_assigment;
                    $orderElementModel->model = $element->getModel(false);
                    $orderElementModel->item_id = $element->getItemId();
                    $orderElementModel->count = $count;
                    $orderElementModel->base_price = $element->getPrice(false);
                    $orderElementModel->price = $element->getPrice();
                    $orderElementModel->options = json_encode($element->getOptions());
                    $orderElementModel->description = '';
                    $orderElementModel->save();

                    $element->getModel()->minusAmount($count);
                }

                $model->reCount();

                if ($model->status === $this->payedStatus) {
                    \Yii::$app->order->setStatus($model->id, $this->halfpayedStatus);
                }
            }
        }
    }
}
