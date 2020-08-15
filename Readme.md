这个库主要是结构化输出结果，避免后端增加或者删除字段导致返回结果字段被增删。

## 环境要求

php>=5.4

## 安装使用

```shell
composer require fanqingxuan/presenter
```

## 使用方法 

```php
require 'vendor/autoload.php';

use Json\TransformerAbstract;
use Json\Presenter;

class BookTransformer extends TransformerAbstract
{
    public function transform($book)
    {
        return [
            'id'      => (int) $book['id'],
            'title'   => $book['title'],
            'year'    => (int) $book['yr'],
        ];
    }
}

$presenter = new Presenter();

//模拟源数据
$books =[
        'id' => '1',
        'title' => 'Hogfather',
        'yr' => '1998',
        'author_name' => 'Philip K Dick',
        'author_email' => 'philip@example.org',
    ];
$data = $presenter->transform($books,new BookTransformer(),false);//不包含引用资源,输出单记录结构
```

输出结果

```shell
Array
(
    [id] => 1
    [title] => Hogfather
    [year] => 1998
)
```

### 功能说明

- 输出单记录结构

  ```php
  //模拟源数据
  $books =[
          'id' => '1',
          'title' => 'Hogfather',
          'yr' => '1998',
          'author_name' => 'Philip K Dick',
          'author_email' => 'philip@example.org',
      ];
  $data = $presenter->transform($books,new BookTransformer(),false);//不包含引用资源,输出单记录结构
  ```

  **注意transform第三个参数是false，表示数据源是单记录结构**

- 输出单记录结构，且结构引入其他资源

  - 方式一

    ```php
    //定义transformer
    class LinkTransformer extends TransformerAbstract
    {
        public function transform($book)
        {
            return [
                'rel' => 'self',
                 'uri' => '/books/'.$book['id'],
            ];
        }
    }
    
    class BookTransformer extends TransformerAbstract
    {
        /**
         * List of resources possible to include
         *
         * @var array
         */
        protected $availableIncludes = [
            'links'
        ];
    
    
    
        public function transform($book)
        {
            return [
                'id'      => (int) $book['id'],
                'title'   => $book['title'],
                'year'    => (int) $book['yr'],
            ];
        }
    
        public function includeLinks($book)
        {
            return $this->item($book,new LinkTransformer);
        }
    }
    
    $presenter = new Presenter();
    
    //模拟源数据
    $books =[
            'id' => '1',
            'title' => 'Hogfather',
            'yr' => '1998',
            'author_name' => 'Philip K Dick',
            'author_email' => 'philip@example.org',
        ];
    $data = $presenter->transform($books,new BookTransformer(['links']),false);//包含引用记录,输出单记录结构
    print_r($data);
    ```

    **Transformer类的构造函数传引用的资源，可以是数组或者字符串**	

  - 方式二

    通过Transformer的setAvailableIncludes()方法设置引用的资源，参考可以是数组或者字符串，字符串用逗号区分多个资源。

    ```php
    //定义transformer
    class LinkTransformer extends TransformerAbstract
    {
        public function transform($book)
        {
            return [
                'rel' => 'self',
                 'uri' => '/books/'.$book['id'],
            ];
        }
    }
    
    class BookTransformer extends TransformerAbstract
    {
        /**
         * List of resources possible to include
         *
         * @var array
         */
        protected $availableIncludes = [
            'links'
        ];
    
    
    
        public function transform($book)
        {
            return [
                'id'      => (int) $book['id'],
                'title'   => $book['title'],
                'year'    => (int) $book['yr'],
            ];
        }
    
        public function includeLinks($book)
        {
            return $this->item($book,new LinkTransformer);
        }
    }
    
    $presenter = new Presenter();
    
    //模拟源数据
    $books =[
            'id' => '1',
            'title' => 'Hogfather',
            'yr' => '1998',
            'author_name' => 'Philip K Dick',
            'author_email' => 'philip@example.org',
        ];
    
    //包含引用记录,输出单记录结构的另一种方式
    $bookTransformer = new BookTransformer;
    $bookTransformer->setAvailableIncludes('links');
    $data = $presenter->transform($books,$bookTransformer,false);//包含引用记录,输出单记录结构
    print_r($data);
    ```

- 输出集合资源

  ```php
  require 'vendor/autoload.php';
  
  use Json\TransformerAbstract;
  use Json\Presenter;
  
  class BookTransformer extends TransformerAbstract
  {
      public function transform($book)
      {
          return [
              'id'      => (int) $book['id'],
              'title'   => $book['title'],
              'year'    => (int) $book['yr'],
          ];
      }
  }
  
  $presenter = new Presenter();
  
  //原始数据
  $userlist = [
  	[
  		'user_id'   => '1',
  		'name'      => 'Json',
  		'email'     => 'fanqingxuan@163.com',
  		'age'       => 32,
          'list'      =>  [
              [
                  'id'    =>  1,
                  'title' =>  '文章1',
                  'text'  =>  '文章1内容',
                  'status'=>  1
              ],
              [
                  'id'    =>  2,
                  'title' =>  '文章2',
                  'text'  =>  '文章2内容',
                  'status'=>  0
              ]
          ]
  	],
  	[
  		'user_id'   => '2',
  		'name'      => '范先生',
  		'email'     => 'json@163.com',
  		'age'       => 29,
          'list'      =>  [
              [
                  'id'    =>  3,
                  'title' =>  '文章3',
                  'text'  =>  '文章3内容',
                  'status'=>  1
              ],
              [
                  'id'    =>  4,
                  'title' =>  '文章4',
                  'text'  =>  '文章4内容',
                  'status'=>  1
              ]
          ]
  	]
  ];
  
  class PostTransformer extends TransformerAbstract
  {
                      
      public function transform(array $post)
      {
          return [
              'post_id'   =>  $post['id'],
               'title'    =>  $post['title']
          ];
      }
      
  }
  
  class UserTransformer extends TransformerAbstract
  {
      protected $availableIncludes = ['posts'];
                      
      public function transform(array $userInfo)
      {
          return [
              'id'            => (int) $userInfo['user_id'],
              'username'      => $userInfo['name'],
              'email'         => $userInfo['email'],
          ];
      }
      
      public function includePosts($userInfo)
      {
          $posts = $userInfo['list'];
          return $this->collection($posts,new PostTransformer);
      }
      
  }
  
  $data = $presenter->transform($userlist,new UserTransformer());//不包含引用资源,输出集合结构
  print_r($data);
  ```

  **注意transform()方法没有专递第三个参数，第三个参数默认为true，表示处理集合**

- 输出集合资源，且引用其它资源结构

  和单资源一样，也有两种方式，两种方式代码如下

  ```php
  //方式1
  $data = $presenter->transform($userlist,new UserTransformer(['posts']));//包含引用记录,输出集合结构
  print_r($data);
  
  //方式2
  //包含引用记录,输出集合结构的另一种方式
  $userTransformer = new UserTransformer;
  $userTransformer->setAvailableIncludes('posts');
  $data = $presenter->transform($userlist,$userTransformer);//包含引用记录,输出集合结构
  print_r($data);
  ```

  