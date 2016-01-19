//
//  CustomerScrollTitleView.swift
//  CustomerScrollTitleViewDemo
//
//  Created by codeSimple on 15/3/17.
//  Copyright (c) 2015年
//

import UIKit

//  MARK: CScrollViewDelegate
@objc protocol CScrollViewDelegate: NSObjectProtocol {
    optional func cscrollViewPageChange(index: Int)
    optional func cscrollViewOnClick(index: Int)
}

private let ScreenWidth = UIScreen.mainScreen().bounds.width
private let ScreenHeight = UIScreen.mainScreen().bounds.height

//  MARK:
class CScrollView: UIView {
    //  MARK: 设置公开变量
    var placeHoderImage: UIImage?
    weak var csDelegate: CScrollViewDelegate?
    var imageViewContentMode: UIViewContentMode = .ScaleAspectFit
    var subViews: [UIView] {
        didSet {
            isImageSubView = false
            configSubViews()
        }
    }
    var imagesUrls: [String] {
        didSet {
            //  监听imageUrl赋值方法
            isImageSubView = true
            configImageView()
        }
    }
    
    //  MARK: 设置私有变量
    private var mainView: UIScrollView?
    private var mViews: [UIImageView]?
    private var pageViewLeft: UIImageView?
    private var pageViewCenter: UIImageView?
    private var pageViewRight: UIImageView?
    private var isStopRoll: Bool?
    private var isImageSubView: Bool?
    
    //  MARK: initializer
    override init(frame: CGRect) {
        self.mViews = []
        self.imagesUrls = []
        self.subViews = []
        super.init(frame: frame)
        //  创建主试图
        self.createMainView()
    }
    
    required init(coder aDecoder: NSCoder) {
        self.mViews = []
        self.imagesUrls = []
        self.subViews = []
        super.init(coder: aDecoder)!
        //  创建主试图
        self.createMainView()
    }
    
    //  MARK: ===== internal methods =====
    //  MARK: 开启自动滚动
    /**
    开启自动滚动
    
    - parameter time: 间隔时间
    */
    func openAutomaticRolling(interval time: NSTimeInterval) {
        //  设置定时器
        NSTimer.scheduledTimerWithTimeInterval(time, target: self, selector: "automaticRolling", userInfo: nil, repeats: true)
    }
    
    //  MARK: 开启长按停止自动滚动
    /**
    开启长按停止自动滚动
    
    - parameter time: 长按时间
    */
    func openLongPanStopRolling(panTime time: NSTimeInterval) {
        guard let mViews = self.mViews else {
            return
        }
        for i in 0 ..< mViews.count {
            let imageView: UIImageView = mViews[i]
            let longPan = UILongPressGestureRecognizer(target: self, action: "longPan:")
            longPan.minimumPressDuration = time
            imageView.addGestureRecognizer(longPan)
        }
    }
    
    //  MARK: ===== private methods =====
    //  MARK: 创建主试图
    private func createMainView() {
        mainView = UIScrollView(frame: CGRect(x: 0, y: 0, width: ScreenWidth + 5, height: ScreenHeight))
        mainView?.delegate = self
        mainView?.pagingEnabled = true
        mainView?.showsHorizontalScrollIndicator = false
        mainView?.contentSize = CGSize(width: ScreenWidth * CGFloat(3) + CGFloat(5) * CGFloat(3), height: ScreenHeight)
        self.addSubview(mainView!)
        
        for index in 0...2 {
            let imageView = createImageView(index)
            switch index {
            case 0:
                pageViewLeft = imageView
                
            case 1:
                pageViewCenter = imageView
                
            case 2:
                pageViewRight = imageView
                
            default:
                break
            }
        }
        pageViewCenter?.tag = 0
        //  将滚动试图移动到中心
        mainView?.contentOffset = CGPoint(x: mainView!.frame.size.width, y: 0)
    }
    
    //  MARK: 创建内容试图
    private func createImageView(index: Int) -> UIImageView {
        let imageView = UIImageView(frame: CGRect(x: CGFloat(index) * (ScreenWidth + CGFloat(5)), y: 0, width: ScreenWidth, height: ScreenHeight))
        imageView.layer.masksToBounds = true
        imageView.userInteractionEnabled = true;
        imageView.contentMode = imageViewContentMode
        imageView.addGestureRecognizer(UITapGestureRecognizer(target: self, action: "tapAction:"));
        mainView?.addSubview(imageView)
        mViews?.append(imageView)
        return imageView
    }
    
    //  MARK: ACTION
    @objc private func automaticRolling() {
        if isStopRoll == true {
            return
        }
        //  滚动时停止交互
        mainView?.userInteractionEnabled = false
        //  固定向右滚动
        UIView.animateWithDuration(2, animations: { () -> Void in
            let frame: CGFloat = self.mainView!.contentOffset.x + self.mainView!.frame.size.width
            self.mainView?.contentOffset = CGPoint(x: frame, y: 0)
            }) { (Bool isOk) -> Void in
                self.scrollViewDidEndDecelerating(self.mainView!);
                self.mainView?.userInteractionEnabled = true;
        }
    }
    
    @objc private func longPan(longPree: UILongPressGestureRecognizer) {
        switch longPree.state {
        case UIGestureRecognizerState.Began:
            isStopRoll = true
            
        case UIGestureRecognizerState.Ended:
            isStopRoll = false
            
        default:
            break
        }
    }
    
    @objc private func tapAction(tap: UITapGestureRecognizer) {
        let imageView = tap.view as! UIImageView
        let tag = imageView.tag
        csDelegate?.cscrollViewOnClick!(tag)
    }
    
    //  MARK: 配置图片
    private func configImageView() {
        //  判断个数
        pageViewCenter?.kf_setImageWithURL(NSURL(string: imagesUrls[0])!, placeholderImage: placeHoderImage)
        mainView?.userInteractionEnabled = true
        if imagesUrls.count == 1 {
            mainView?.userInteractionEnabled = false
        } else if imagesUrls.count == 2 {
            pageViewLeft?.kf_setImageWithURL(NSURL(string: imagesUrls[1])!, placeholderImage: placeHoderImage)
            pageViewRight?.kf_setImageWithURL(NSURL(string: imagesUrls[1])!, placeholderImage: placeHoderImage)
        } else {
            pageViewLeft?.kf_setImageWithURL(NSURL(string: imagesUrls[imagesUrls.count - 1])!, placeholderImage: placeHoderImage)
            pageViewRight?.kf_setImageWithURL(NSURL(string: imagesUrls[1])!, placeholderImage: placeHoderImage)
        }
    }
    //  MARK: 配置子试图
    private func configSubViews() {
        let first = subViews.startIndex
        let last = subViews.endIndex - 1
        pageViewCenter!.addSubview(subViews[first])
        pageViewLeft!.addSubview(subViews[last])
        pageViewRight!.addSubview(subViews[1])
    }
    
    //  MARK: 移动试图向左
    private func allArticlesMoveLeft(pageWidth: CGFloat) {
        
        let lastCenterTag: Int = pageViewCenter!.tag
        
        let tempView = pageViewLeft!
        pageViewLeft = pageViewCenter
        pageViewCenter = pageViewRight
        pageViewRight = tempView
        
        var count = 0;
        if isImageSubView! {
            count = imagesUrls.count - 1
        } else {
            count = subViews.count - 1
        }
        
        if lastCenterTag == count {
            pageViewCenter?.tag = 0
        } else {
            pageViewCenter?.tag = lastCenterTag + 1
        }
        //  重新附图,中,左图不变,改变右图
        var nextTag = pageViewCenter!.tag + 1
        
        //  更新图
        if !isImageSubView! {
            if nextTag == subViews.count {
                nextTag = 0
            }
            pageViewRight!.addSubview(subViews[nextTag])
        } else {
            if nextTag == imagesUrls.count {
                nextTag = 0
            }
            pageViewRight?.kf_setImageWithURL(NSURL(string: imagesUrls[nextTag])!, placeholderImage: placeHoderImage)
        }
    }
    
    //  MARK: 移动试图向右
    private func allArticlesMoveRight(pageWidth: CGFloat) {
        
        let lastCenterTag = pageViewCenter!.tag
        
        let tempView: UIImageView = pageViewRight!
        pageViewRight = pageViewCenter
        pageViewCenter = pageViewLeft
        pageViewLeft = tempView
        
        var count = 0
        if isImageSubView! {
            count = imagesUrls.count - 1
        } else {
            count = subViews.count - 1
        }
        
        if lastCenterTag == 0 {
            pageViewCenter?.tag = count
        } else {
            pageViewCenter?.tag = lastCenterTag - 1
        }
        
        //  重新附图,中,左图不变,改变右图
        var nextTag = pageViewCenter!.tag - 1
        
        //  更新图
        if subViews.count != 0 {
            if nextTag < 0 {
                nextTag = subViews.count - 1
            }
            pageViewLeft!.addSubview(subViews[nextTag])
        } else {
            if nextTag < 0 {
                nextTag = imagesUrls.count - 1
            }
            pageViewLeft?.kf_setImageWithURL(NSURL(string: imagesUrls[nextTag])!, placeholderImage: placeHoderImage)
        }
        
    }
    
    //  MARK: 重新设置视图位置
    private func set_frame_center() {
        let x: CGFloat = mainView!.frame.size.width
        let y: CGFloat = 0
        let width = pageViewCenter!.frame.size.width
        let height = pageViewCenter!.frame.size.height
        pageViewCenter?.frame = CGRect(x: x, y: y, width: width, height: height)
    }
    
    private func set_frame_Left() {
        let x: CGFloat = 0
        let y: CGFloat = 0
        let width = pageViewLeft!.frame.size.width
        let height = pageViewLeft!.frame.size.height
        pageViewLeft?.frame = CGRect(x: x, y: y, width: width, height: height)
    }
    
    private func set_frame_Right() {
        let x: CGFloat = mainView!.frame.size.width * 2
        let y: CGFloat = 0
        let width = pageViewRight!.frame.size.width
        let height = pageViewRight!.frame.size.height
        pageViewRight?.frame = CGRect(x: x, y: y, width: width, height: height)
    }
}

//  MARK: - Scroll View Delegate
extension CScrollView: UIScrollViewDelegate {
    func scrollViewDidEndDecelerating(scrollView: UIScrollView) {
        let pageWidth = scrollView.frame.size.width
        let page = Int(scrollView.contentOffset.x / pageWidth)
        if page == 1 {
            //  保持在中间不需要移动
            return
        } else if page == 0 {
            allArticlesMoveRight(pageWidth)
        } else {
            allArticlesMoveLeft(pageWidth)
        }
        set_frame_center()
        set_frame_Right()
        set_frame_Left()
        scrollView.setContentOffset(CGPoint(x: pageWidth, y: 0), animated: false)
        csDelegate?.cscrollViewPageChange!(pageViewCenter!.tag)
    }
}
