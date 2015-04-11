//
//  ViewController.m
//  CScrollView
//
//  Created by 张鼎辉 on 15/4/11.
//  Copyright (c) 2015年 四海道达网络科技有限公司. All rights reserved.
//

#import "ViewController.h"
#import "CScrollView-Swift.h"
@interface ViewController ()<CScrollViewDelegate>

@end

@implementation ViewController

- (void)viewDidLoad {
    [super viewDidLoad];
    self.view.backgroundColor = [UIColor brownColor];
    // Do any additional setup after loading the view, typically from a nib.
    
//    [self webPageUrls];
    [self customerViews];
}


#pragma mark 网络连接展示
- (void)webPageUrls{
    NSArray *urls = @[@"http://img4.duitang.com/uploads/item/201407/13/20140713095140_LusMk.thumb.700_0.jpeg",@"http://img4.duitang.com/uploads/item/201407/06/20140706090107_H3Tyd.jpeg",@"http://img4q.duitang.com/uploads/item/201407/21/20140721143254_reBYa.jpeg",@"http://i2.img.969g.com/pcgame/imgx2015/03/24/289_143056_7f39a_lit.jpg"];
    CGFloat y = ([UIScreen mainScreen].bounds.size.height - 250)/2;
    CScrollView *sView = [[CScrollView alloc] initWithFrame:CGRectMake(0, y, [UIScreen mainScreen].bounds.size.width, 250)];
    sView.imagesUrls = urls;
    sView.csDelegate = self;
    [sView openAutomaticRolling];//打开自动滚动
    [sView openLongPanStopRolling:1];//打开长按停止自动滚动
    [self.view addSubview:sView];
}

#pragma mark 自定义试图展示
- (void)customerViews{
    CGFloat y = ([UIScreen mainScreen].bounds.size.height - 250)/2;
    
    CGRect frame = CGRectMake(0, 0, [UIScreen mainScreen].bounds.size.width, 250);
    UIImageView *view1 = [[UIImageView alloc] initWithFrame:frame];
    UIImageView *view2 = [[UIImageView alloc] initWithFrame:frame];
    UIImageView *view3 = [[UIImageView alloc] initWithFrame:frame];
    UIImageView *view4 = [[UIImageView alloc] initWithFrame:frame];
    view1.image = [UIImage imageNamed:@"1.jpg"];
    view2.image = [UIImage imageNamed:@"2.jpg"];
    view3.image = [UIImage imageNamed:@"3.jpg"];
    view4.image = [UIImage imageNamed:@"4.jpg"];
    
    CScrollView *sView = [[CScrollView alloc] initWithFrame:CGRectMake(0, y, [UIScreen mainScreen].bounds.size.width, 250)];
    sView.subViews = @[view1,view2,view3,view4];
    sView.csDelegate = self;
    [sView openAutomaticRolling];//打开自动滚动
    [sView openLongPanStopRolling:1];//打开长按停止自动滚动
    [self.view addSubview:sView];
}


#pragma mark Delegate
- (void)cscrollViewOnClick:(NSInteger)index{
    
}
- (void)cscrollViewPageChange:(NSInteger)index{
    
}

@end
