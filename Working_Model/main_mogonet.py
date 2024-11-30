""" Example for MOGONET classification
"""
from train_test import train_test, split_csv_line_by_line

if __name__ == "__main__":    

    # This is the main "integration" point
    # ------------------------------------------------------------------------------------------------------------------------
    # train_test(): This is the function that adds training and testing functionality
    # ------------------------------------------------------------------------------------------------------------------------
    
    # When the user uploads the folder onto our website, we get the name of the folder from that
    # USER INPUT: data_folder 
    data_folder = 'ROSMAP'

    # view_list = [1,2,3]
    # Guide to view_list, we might want to let the user choose the combination
    # ------------------------------------------------------------------------------------------------------------------------
    # 1: GCN mRNA
    # 2: GCN meth
    # 3: GCN miRNA
    # 1,2 OR 1,2,3 OR 1,3: Cross Omics Discovery Tensor -> View Correlation Discorvery Network -> Final Prediction
    # ------------------------------------------------------------------------------------------------------------------------
    # USER INPUT: view_list 
    view_list = [1]
    view_to_file_map = {
        1: f"{data_folder}/1_new_patients.csv",
        2: f"{data_folder}/2_new_patients.csv",
        3: f"{data_folder}/3_new_patients.csv"
    }

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

    input_labels_file = f"{data_folder}/all_labels.csv"
    # USER INPUT: test_size
    test_size = 70
    # USER INPUT: train_size
    train_size = 280

    # Iterate over the views specified in view_list and process the corresponding files
    for view in view_list:
        if view in view_to_file_map:
            input_data_file = view_to_file_map[view]
            prefix = str(view)  # Use the view number as the prefix for output files

            # Split the file
            split_csv_line_by_line(input_data_file, input_labels_file, test_size, train_size, data_folder, prefix)
        else:
            print(f"Warning: View {view} does not have a corresponding data file.")

    train_test(data_folder, view_list, num_class,
               lr_e_pretrain, lr_e, lr_c, 
               num_epoch_pretrain, num_epoch)