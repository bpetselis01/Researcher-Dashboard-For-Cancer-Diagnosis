""" Example for MOGONET classification
"""
from train_test import train_test

if __name__ == "__main__":    

    # This is the main "integration" point
    # ------------------------------------------------------------------------------------------------------------------------
    # train_test(): This is the function that adds training and testing functionality
    # ------------------------------------------------------------------------------------------------------------------------
    
    # When the user uploads the folder onto our website, we get the name of the folder too
    data_folder = 'BRCA'

    # view_list = [1,2,3]
    # Guide to view_list, we might want to let the user choose the combination
    # ------------------------------------------------------------------------------------------------------------------------
    # 1: GCN mRNA
    # 2: GCN meth
    # 3: GCN miRNA
    # 1,2 OR 1,2,3 OR 1,3: Cross Omics Discovery Tensor -> View Correlation Discorvery Network -> Final Prediction
    # ------------------------------------------------------------------------------------------------------------------------
    view_list = [1]

    num_epoch_pretrain = 500
    num_epoch = 2500
    lr_e_pretrain = 1e-3
    lr_e = 5e-4
    lr_c = 1e-3

    # Guide to num_class, we might want to let the user choose the number of classes
    # ------------------------------------------------------------------------------------------------------------------------
    # 2: Binary Classification (ROSMAP has 2 Classes, Has Alzheimers or Doesn't have Alzheimers)
    # 5: Multi-Class Classification (BRCA has 5 Classes)
    # ------------------------------------------------------------------------------------------------------------------------
    
    if data_folder == 'ROSMAP':
        num_class = 2
    if data_folder == 'BRCA':
        num_class = 5
    
    train_test(data_folder, view_list, num_class,
               lr_e_pretrain, lr_e, lr_c, 
               num_epoch_pretrain, num_epoch)             